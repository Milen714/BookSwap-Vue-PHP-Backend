<?php
namespace App\config;

use PDO;
use PDOException;

class DatabaseConfig{
    private ?PDO $pdo = null;

    protected function connect(){
        $dbHost = getenv('DB_HOST') ?: 'mysql';
        $dbName = getenv('DB_NAME') ?: 'developmentdb';
        $dbPort = getenv('DB_PORT') ?: '3306';
        $username = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: 'secret123';
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}";
        try{
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->pdo;
        }catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }
    protected function disconnect(){
        $this->pdo = null;
    }
}