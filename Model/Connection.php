<?php

namespace Model;

use PDO;
use PDOException;

class Connection
{
    private static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            try {
                $host = 'localhost';
                $dbname = 'todo';
                $user = 'root';
                $pass = '';
                $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";

                self::$instance = new PDO($dsn, $user, $pass);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die('Erro de conexão: ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
?>