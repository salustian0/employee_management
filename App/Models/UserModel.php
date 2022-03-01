<?php

namespace App\Models;

use App\Entity\UserEntity;
use App\system\dao\Connection;
use App\system\Utils\Security;
use http\Client\Curl\User;
use \PDO;

class UserModel extends Connection
{

    public function __construct($db = 'default')
    {
        parent::__construct($db);
        $this->table = 'users';
    }

    /**
     * Retorna usuário por username
     * @param string $username
     * @return UserEntity
     */
    public function getUserByUsername(string $username) : UserEntity{
        $query = "SELECT u.* FROM users u WHERE u.username = :username";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchObject(UserEntity::class);
    }

    /**
     * @param int $idUser
     * @return UserEntity
     */
    public function getUserById(int $idUser){
        $query = "SELECT u.* FROM users u WHERE u.id = :idUser";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":idUser", $idUser, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchObject(UserEntity::class);
    }
    /**
     * Registro de usuário
     * @param UserEntity $userEntity
     * @return string
     */
    public function addUser(UserEntity $userEntity){
        return $this->insert($userEntity);
    }

    /**
     * Atualização de dados
     * @param UserEntity $userEntity
     * @return mixed
     */
    public function updateUser(UserEntity $userEntity){
        return $this->update($userEntity, array(
            'id' => $userEntity->getId(),
        ));
    }

    /**
     * @return UserEntity[]
     */
    public function getAllUsers(){
        $query = "SELECT * FROM users";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, UserEntity::class);
    }

    /**
     * Verifica se o registro existe
     * @param int $id
     * @return mixed
     */
    public function hasById(int $id){
        $query = "SELECT 1 FROM users WHERE id = :id";
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
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id',$id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Verifica existencia do usuário por username
     * @param string $username
     * @return mixed
     */
    public function hasByUsername(string $username, int $idNot = null){
        $query = "SELECT 1 FROM users WHERE username = :username";

        if(!empty($idNot)){
            $query .= PHP_EOL." AND id != :id";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':username',$username, PDO::PARAM_STR);

        if(!empty($idNot)){
            $stmt->bindValue(':id',$idNot, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }
}