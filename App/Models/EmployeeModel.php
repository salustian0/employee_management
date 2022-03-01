<?php

namespace App\Models;

use App\Entity\EmployeeEntity;
use App\system\dao\Connection;
use \PDO;

class EmployeeModel extends Connection
{

    public function __construct($db = 'default')
    {
        parent::__construct($db);
        $this->table = 'employees';
    }


    /**
     * @param int $idEmployee
     * @return EmployeeEntity | false
     */
    public function getEmployeeById(int $idEmployee){
        $query = "SELECT e.* FROM employees e WHERE e.id = :idEmployee";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":idEmployee", $idEmployee, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchObject(EmployeeEntity::class);
    }

    /**
     * Registro de funcionário
     * @param EmployeeEntity $employeeEntity
     * @return false|string
     * @throws \Exception
     */
    public function addEmployee(EmployeeEntity $employeeEntity){
        return $this->insert($employeeEntity);
    }

    /**
     * Atualização de dados
     * @param EmployeeEntity $employeeEntity
     * @return bool
     */
    public function updateEmployee(EmployeeEntity $employeeEntity){
        return $this->update($employeeEntity, array(
            'id' => $employeeEntity->getId(),
        ));
    }

    /**
     * @return EmployeeEntity[]
     */
    public function getAllEmployees(){
        $query = "SELECT e.id as idIndex, e.* FROM employees e";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_UNIQUE, EmployeeEntity::class);
    }

    /**
     * Verifica se o registro existe
     * @param int $id
     * @return mixed
     */
    public function hasById(int $id){
        $query = "SELECT 1 FROM employees WHERE id = :id";
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
        $query = "DELETE FROM employees WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id',$id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Verifica existencia do funcionário por cpf
     * @param string $cpf
     * @return mixed
     */
    public function hasByCpf(string $cpf, int $idNot = null){
        $query = "SELECT 1 FROM employees WHERE cpf = :cpf";

        if(!empty($idNot)){
            $query .= PHP_EOL." AND id != :id";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':cpf',$cpf, PDO::PARAM_STR);

        if(!empty($idNot)){
            $stmt->bindValue(':id',$idNot, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Verifica existencia do funcionário por email
     * @param string $cpf
     * @return mixed
     */
    public function hasByEmail(string $email, int $idNot = null){
        $query = "SELECT 1 FROM employees WHERE email = :email";

        if(!empty($idNot)){
            $query .= PHP_EOL." AND id != :id";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':email',$email, PDO::PARAM_STR);

        if(!empty($idNot)){
            $stmt->bindValue(':id',$idNot, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }
}