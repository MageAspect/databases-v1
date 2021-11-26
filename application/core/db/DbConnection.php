<?php


namespace application\core\db;


use PDO;


class DbConnection {
    protected static PDO $dbConnection;

    public static function getConnection(): PDO {

        if (empty(static::$dbConnection)) {
            $config = require $_SERVER['DOCUMENT_ROOT'] . '/application/config/db.php';
            static::$dbConnection = new PDO(
                    'pgsql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';port=5432',
                    $config['user'],
                    $config['password'],
                    $config['options']
            );
        }
        return static::$dbConnection;
    }
}