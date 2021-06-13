<?php


namespace application\core;


use PDO;


class DbConnection {
    protected static PDO $dbConnection;

    public static function getConnection(): PDO {

        if (empty(static::$dbConnection)) {
            $config = require $_SERVER['DOCUMENT_ROOT'] . '/application/config/db.php';
            static::$dbConnection = new PDO(
                    'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=' . $config['charset'],
                    $config['user'],
                    $config['password'],
                    $config['options']
            );
        }
        return static::$dbConnection;
    }
}