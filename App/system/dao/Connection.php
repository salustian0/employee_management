<?php

namespace App\system\dao;

use \PDO;

abstract class Connection
{

    private string $db_host;
    private string $db_name;
    private string $user;
    private string $password;

    private PDO $pdo;

    public function __construct($db = 'default')
    {
        if (!defined("DB_SETTINGS") || !isset(DB_SETTINGS[$db])) {
            throw new \Exception("Configuração de banco de dados \"{$db}\" não encontrada!");
        }

        $dbSettings = DB_SETTINGS[$db] ?? [];
        $this->db_host = $dbSettings['DB_HOST'] ?? "";
        $this->db_name = $dbSettings['DB_NAME'] ?? "";
        $this->user = $dbSettings['DB_USER'] ?? "";
        $this->password = $dbSettings['DB_PASS'] ?? "";

        $this->connect();
    }

    /**
     * Conexão ao banco
     */
    private function connect()
    {
        try {
            $this->pdo = new PDO("mysql:host={$this->db_host};dbname={$this->db_name};charset=utf8", $this->user, $this->password);
        } catch (\PDOException $ex) {
            die("Houve um erro durante a conexão ao banco de dados: {$ex->getMessage()}");
        }
    }
}