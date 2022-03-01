<?php

namespace App\Models;

use App\Entity\TimeEntity;
use App\system\dao\Connection;
use \PDO;

class TimeModel extends Connection
{

    public function __construct($db = 'default')
    {
        parent::__construct($db);
        $this->table = 'time';
    }


    /**
     * @param int $idTime
     * @return TimeEntity
     */
    public function getTimeById(int $idTime){
        $query = "SELECT t.* FROM `time` t  WHERE t.id = :idTime";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":idTime", $idTime, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchObject(TimeEntity::class);
    }

    /**
     * Registro de time
     * @param TimeEntity $timeEntity
     * @return false|string
     * @throws \Exception
     */
    public function addTime(TimeEntity $timeEntity){
        return $this->insert($timeEntity);
    }

    /**
     * Atualização de dados
     * @param TimeEntity $timeEntity
     * @return bool
     */
    public function updateTime(TimeEntity $timeEntity){
        return $this->update($timeEntity, array(
            'id' => $timeEntity->getId(),
        ));
    }

    /**
     * @return TimeEntity[]
     */
    public function getAllTime(array $filters = array()){
        $bindValues = [];
        $query = "SELECT t.* FROM `time` t";

        if(!empty($filters)){

            if(isset($filters['order_name'])){
                $query .= " INNER JOIN employees e ON e.id = `t`.employee_id ";
            }

            switch ($filters){
                case (isset($filters['period_start']) && isset($filters['period_end'])):
                    $query .= " WHERE date between :period_start AND :period_end";
                    $bindValues[] = array('param' => ':period_start', 'value' => $filters['period_start']);
                    $bindValues[] = array('param' => ':period_end', 'value' => $filters['period_end']);
                    break;
                case  isset($filters['period_start']):
                    $subQueryMinDate = "(SELECT MAX(date) from time)";
                    $query .= " WHERE date between :period_start AND {$subQueryMinDate} ";
                    $bindValues[] = array('param' => ':period_start', 'value' => $filters['period_start']);
                    break;
                case isset($filters['period_end']):
                    $subQueryMinDate = "(SELECT MIN(date) from time)";
                    $query .= " WHERE date between {$subQueryMinDate} AND :period_end";
                    $bindValues[] = array('param' => ':period_end', 'value' => $filters['period_end']);
                    break;
            }

            if(isset($filters['order_date']) || isset($filters['order_name'])){
                switch ($filters){
                    case (isset($filters['order_name']) && isset($filters['order_date'])):
                        $query .= " ORDER BY e.name {$filters['order_name']}, t.date {$filters['order_date']}";
                        break;
                    case  isset($filters['order_name']):
                        $query .= " ORDER BY e.name {$filters['order_name']}";
                        break;
                    case isset($filters['order_date']):
                        $query .= " ORDER BY t.date {$filters['order_date']}";
                        break;
                }
            }
        }

        $stmt = $this->pdo->prepare($query);

        foreach ($bindValues as $bindValue){
            $stmt->bindValue($bindValue['param'], $bindValue['value']);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, TimeEntity::class);
    }

    /**
     * Verifica se o registro existe
     * @param int $id
     * @return mixed
     */
    public function hasById(int $id){
        $query = "SELECT 1 FROM `time` WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id',$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Remove um registro por id
     * @param int $id
     * @return bool
     */
    public function delete(int $id){
        $query = "DELETE FROM `time` WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id',$id, PDO::PARAM_INT);
        return $stmt->execute();
    }

}