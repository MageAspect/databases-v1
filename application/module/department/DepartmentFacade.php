<?php

/**
 * @author Mark Prohorov <mark@intervolga.ru>
 */


namespace application\module\department;


use application\core\db\Db;
use application\core\db\DbQueryException;
use application\module\user\control\exception\UserStoreException;
use application\module\user\control\UserStore;
use Exception;


class DepartmentFacade {
    private UserStore $userStore;
    private Db $db;

    public function __construct() {
        $this->userStore = new UserStore();
        $this->db = new Db();
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

        try {
            $dbDepartments = $this->db->query(
                    "SELECT id, name, description, head_id FROM departments WHERE id IN ({$this->db->getINPlaceholder($ids)})",
                    $ids
            );
            $departments = array();
            while ($departmentInfo = $dbDepartments->fetch()) {
                $d = new Department();

                $d->id = $departmentInfo['id'];
                $d->name = $departmentInfo['name'];
                $d->description = $departmentInfo['description'];
                $d->head = $departmentInfo['head_id'] > 0 ? $this->userStore->getUserById($departmentInfo['head_id']) : null;

                $departments[$d->id] = $d;
            }

            return $departments;
        } catch (DbQueryException | UserStoreException $e) {
            throw new DepartmentFacadeException('Ошибка получения списка подразделей', 0, $e);
        }
    }

    /**
     * @return Department[]
     * @throws DepartmentFacadeException
     */
    public function getAllDepartments(): array {
        try {
            $dbDepartmentsIds = $this->db->query("SELECT id FROM departments");

            $departmentsIds = array();
            while ($row = $dbDepartmentsIds->fetch()) {
                $departmentsIds[] = $row['id'];
            }

            return !empty($departmentsIds) ? $this->getDepartments($departmentsIds) : array();
        } catch (DbQueryException $e) {
            throw new DepartmentFacadeException('Ошибка получения списка всех подразделей', 0, $e);
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

    /**
     * @throws DepartmentFacadeException
     */
    public function updateDepartment(Department $department): void {
        try {
            $this->db->query(
                    "
                    UPDATE departments SET name = :name, description = :description, head_id = :head_id WHERE id = :id
                    ",
                    array(
                            'id' => $department->id,
                            'name' => $department->name,
                            'description' => $department->description,
                            'head_id' => $department->head->id,
                    )
            );
        } catch (DbQueryException $e) {
            throw new DepartmentFacadeException('Ошибка обновления подразделея', 0, $e);
        }
    }

    /**
     * @throws DepartmentFacadeException
     */
    public function addDepartment(Department $department): void {
        try {
            $this->db->query(
                    "
                    INSERT INTO departments (name, description, head_id) VALUES (:name, :description, :head_id)
                    ",
                    array(
                            'name' => $department->name,
                            'description' => $department->description,
                            'head_id' => $department->head->id,
                    )
            );
        } catch (DbQueryException $e) {
            throw new DepartmentFacadeException('Ошибка обновления подразделея', 0, $e);
        }
    }

    /**
     * @throws DepartmentFacadeException
     */
    public function addUserToDepartment(int $userId, int $departmentId) {
        try {
            $this->db->query(
                    "
                    INSERT INTO user_department (user_id, department_id) VALUES (:user_id, :department_id)
                    ",
                    array(
                            'user_id' => $userId,
                            'department_id' => $departmentId,
                    )
            );
        } catch (DbQueryException $e) {
            throw new DepartmentFacadeException('Ошибка привязки пользователя к подразделению', 0, $e);
        }
    }

    /**
     * @throws DepartmentFacadeException
     */
    public function removeUserFromDepartment(int $userId, int $departmentId) {
        try {
            $this->db->query(
                    "
                    DELETE FROM user_department WHERE user_id = :user_id AND department_id = :department_id
                    ",
                    array(
                            'user_id' => $userId,
                            'department_id' => $departmentId,
                    )
            );
        } catch (DbQueryException $e) {
            throw new DepartmentFacadeException('Ошибка отвязки пользователя от подразделения', 0, $e);
        }
    }
}