<?php


namespace application\core;


use application\core\exception\DbQueryException;
use PDO;
use PDOException;
use PDOStatement;


class Db {
    private PDO $db;

    public function __construct() {
        $this->db = DbConnection::getConnection();
    }

    /**
     * Подготавливает и выполняет sql запрос
     * @throws DbQueryException
     */
    public function query(string $sql, array $params = array()): PDOStatement {
        $statement = $this->db->prepare($sql);

        if (!$statement) {
            throw new DbQueryException('Database server cannot successfully prepare the statement');
        }

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                if (preg_match("#^\d*$#", $key)) {
                    $statement->bindValue($key + 1, $value);
                } else {
                    $statement->bindValue(":$key", $value);
                }
            }
        }

        try {
            $statement->execute();
        } catch (PDOException $e) {
            throw new DbQueryException($statement->errorInfo(), $statement->errorCode(), $e);
        }

        return $statement;
    }

    public function lastInsertId(): ?int {
        return $this->db->lastInsertId() ?: null;
    }
}