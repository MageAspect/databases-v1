<?php

namespace application\module\admin\sqlQuery;


use application\core\db\Db;
use application\core\db\DbQueryException;


class SqlQueryFacade {
    private Db $db;

    public function __construct() {
        $this->db = new Db();
    }

    /**
     * @return SqlHistoryEntry[]
     * @throws SqlQueryFacadeException
     */
    public function getSqlHistory(int $userId): array {
        try {
            $dbEntries = $this->db->query(
                    "
                    SELECT id, sql_query, execution_datetime 
                    FROM sql_query_history
                    WHERE user_id = :user_id
                ",
                    array(
                            'user_id' => $userId
                    )
            );
        } catch (DbQueryException $e) {
            throw new SqlQueryFacadeException('Ошибка получения истории sql запросов', 0, $e);
        }


        $entries = array();
        while ($entryInfo = $dbEntries->fetch()) {
            $e = new SqlHistoryEntry();
            $e->id = $entryInfo['id'];
            $e->sql = $entryInfo['sql_query'];
            $e->executionDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $entryInfo['execution_datetime']);

            $entries[$e->id] = $e;
        }

        return $entries;
    }

    /**
     * @throws SqlQueryFacadeException
     */
    public function executeRawQuery(string $sql, int $userId): array {
        try {
            $res = $this->db->query($sql)->fetchAll();

            $this->db->query(
                    "
                        INSERT INTO sql_query_history (user_id, sql_query) VALUES (:user_id, :sql)
                    ",
                    array(
                            'user_id' => $userId,
                            'sql' => $sql
                    )
            );
            return $res;
        } catch (DbQueryException $e) {
            throw new SqlQueryFacadeException('Ошибка выполнения sql запроса', 0, $e);
        }
    }
}