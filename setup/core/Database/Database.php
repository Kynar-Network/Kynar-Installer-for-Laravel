<?php
namespace App\Database;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $config = require __DIR__ . '/../../configs/database.php';
        $this->connection = new \PDO(
            "mysql:host={$config['host']};dbname={$config['database']};charset=utf8",
            $config['username'],
            $config['password']
        );
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
