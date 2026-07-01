<?php
namespace App\config;

use PDO;
use PDOException;

class DatabaseConfig{
    private ?PDO $pdo = null;

    protected function connect(){
        // $dbHost = getenv('POSTGRES_HOST') ?: 'localhost';
        // $dbName = getenv('POSTGRES_DB') ?: 'developmentdb';
        // $dbPort = getenv('POSTGRES_PORT') ?: '5432';
        // $username = getenv('POSTGRES_USER') ?: 'postgres';
        // $password = getenv('POSTGRES_PASSWORD') ?: 'password';
        // $dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";

        $host = getenv('POSTGRES_HOST') ?: 'postgres';
        $port = getenv('POSTGRES_PORT') ?: '5432';
        $database = getenv('POSTGRES_DB') ?: 'bookswap_vectors';
        $username = getenv('POSTGRES_USER') ?: 'developer';
        $password = getenv('POSTGRES_PASSWORD') ?: 'secret123';
        $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
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