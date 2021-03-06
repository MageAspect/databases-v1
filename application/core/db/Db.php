<?php


namespace application\core\db;


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
            throw new DbQueryException($e->getMessage() . '|' . implode($statement->errorInfo()), 0, $e);
        }

        return $statement;
    }

    public function lastInsertId(): ?int {
        return $this->db->lastInsertId() ?: null;
    }

    public function getINPlaceholder(array $inItems): string {
        if (empty($inItems)) {
            return '';
        }
        
        return str_repeat('?,', count($inItems) - 1) . '?';
    }
}