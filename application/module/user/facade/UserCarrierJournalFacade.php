<?php

namespace application\module\user\facade;


use application\core\db\Db;
use application\core\db\DbQueryException;
use application\module\department\DepartmentFacade;
use application\module\department\DepartmentFacadeException;
use application\module\department\DepartmentNotFoundException;
use application\module\user\entity\JournalEntry;
use DateTime;


class UserCarrierJournalFacade {
    private DepartmentFacade $departmentFacade;
    private UserFacade $userFacade;
    private Db $db;

    public function __construct() {
        $this->userFacade = new UserFacade();
        $this->departmentFacade = new DepartmentFacade();
        $this->db = new Db();
    }

    /**
     * @param int $userId
     * @return JournalEntry[]
     * @throws UserCarrierJournalFacadeException
     */
    public function getUserCarrierJournal(int $userId): array {
        try {
            $dbEntries = $this->db->query(
                    "
                    SELECT id, user_id, department_id, salary, position, start_datetime, end_datetime, 
                           DATEDIFF(IF(end_datetime IS NULL, NOW(), end_datetime), start_datetime) as days_in_work
                    FROM users_career_journal
                    WHERE user_id = :user_id
                ",
                    array(
                            'user_id' => $userId
                    )
            );
        } catch (DbQueryException $e) {
            throw new UserCarrierJournalFacadeException('Не удалось получить карьерный журнал пользователя');
        }

        $entries = array();
        while ($entryInfo = $dbEntries->fetch()) {
            $e = new JournalEntry();
            $e->id = $entryInfo['id'];
            $e->userId = $entryInfo['user_id'];
            $e->salary = $entryInfo['salary'];
            $e->position = $entryInfo['position'];
            $e->daysInWork = $entryInfo['days_in_work'] ?: 0;
            $e->startDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $entryInfo['start_datetime']);
            $e->endDateTime = $entryInfo['end_datetime'] !== null
                    ? DateTime::createFromFormat('Y-m-d H:i:s', $entryInfo['end_datetime'])
                    : null;

            try {
                $e->department = $entryInfo['department_id'] !== null
                        ? $this->departmentFacade->getDepartmentById($entryInfo['department_id'])
                        : null;
            } catch (DepartmentNotFoundException $e) {
                $e->department = null;
            } catch (DepartmentFacadeException $e) {
                throw new UserCarrierJournalFacadeException('Не удалось получить отдел в записи карьерного журнала пользователя');
            }

            $entries[$e->id] = $e;
        }

        return $entries;
    }
}