<?php

/**
 * @author Mark Prohorov <mark@intervolga.ru>
 */


namespace application\module\department;


use application\core\db\Db;
use application\core\db\DbQueryException;
use application\module\user\control\UserStore;
use Exception;


class DepartmentFacade {
    private UserStore $userStore;
    private Db $db;

    public function __construct() {
        $this->userStore = new UserStore();
    }

    /**
     * @param int[] $ids
     * @return Department[]
     * @throws DepartmentFacadeException
     */
    public function getDepartments(array $ids): array {
        if (empty($ids)) {
            return array();
        }

        $inBody = implode(',', $ids);
        try {
            $dbDepartments = $this->db->query(
                    "SELECT id, name, description, head_id FROM departments WHERE id in (:ids)",
                    array('ids' => $inBody)
            );
            $departments = array();
            while ($departmentInfo = $dbDepartments->fetch()) {
                $d = new Department();

                $d->id = $departmentInfo['id'];
                $d->name = $departmentInfo['name'];
                $d->description = $departmentInfo['description'];
                try {
                    $d->head = $departmentInfo['head_id'] > 0 ? $this->userStore->getUserById($departmentInfo['head_id']) : null;
                } catch (Exception $e) {
                    $d->head = null;
                }

                $departments[$d->id] = $d;
            }

            return $departments;
        } catch (DbQueryException $e) {
            throw new DepartmentFacadeException('Ошибка получения списка подразделей', 0, $e);
        }
    }

    /**
     * @param int $userId
     * @return Department[]
     * @throws DepartmentFacadeException
     */
    public function getUserDepartments(int $userId): array {
        try {
            $dbDepartmentsIds = $this->db->query(
                    "SELECT department_id  FROM user_department WHERE user_id in (:id)",
                    array('id' => $userId)
            );

            $departmentsIds = array();
            while ($row = $dbDepartmentsIds->fetch()) {
                $departmentsIds[] = $row['department_id'];
            }

            return !empty($departmentsIds) ? $this->getDepartments($departmentsIds) : array();
        } catch (DbQueryException $e) {
            throw new DepartmentFacadeException('Ошибка получения списка подразделей для пользователя', 0, $e);
        }
    }
}