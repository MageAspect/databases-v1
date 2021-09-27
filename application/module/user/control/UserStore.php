<?php


namespace application\module\user\control;


use application\core\db\Db;
use application\core\db\DbQueryException;
use application\module\user\control\exception\UserNotFoundException;
use application\module\user\control\exception\UserStoreException;
use application\module\user\entity\User;


class UserStore {

    private Db $db;

    public function __construct(?Db $db = null) {
        $this->db = $db ?? new Db();
    }

    /**
     * @throws UserStoreException
     * @throws UserNotFoundException
     */
    public function getUserByLogin(string $login): User {
        try {
            $dbUser = $this->db->query(
                    "
                        SELECT id, login, hashed_password, 
                               email, name, last_name, patronymic, is_admin 
                        FROM users 
                        WHERE login = :login
                    ",
                    array('login' => $login)
            );
            $userInfo = $dbUser->fetch();

            if (empty($userInfo)) {
                throw new UserNotFoundException();
            }
            $user = new User();

            $user->id = $userInfo['id'];
            $user->email = $userInfo['email'];
            $user->login = $userInfo['login'];
            $user->name = $userInfo['name'];
            $user->lastName = $userInfo['last_name'];
            $user->patronymic = $userInfo['patronymic'];
            $user->isAdmin = $userInfo['is_admin'];
            return $user;
        } catch (DbQueryException $ex) {
            throw new UserStoreException('Ошибка получения пользователя из базы данных', 0, $ex);
        }
    }

    /**
     * @throws UserStoreException
     * @throws UserNotFoundException
     */
    public function getUserHashedPassword(int $id): string {
        try {
            $dbUser = $this->db->query(
                    "
                        SELECT hashed_password
                        FROM users 
                        WHERE id = :id
                    ",
                    array('id' => $id)
            );
            $userInfo = $dbUser->fetch();

            if (empty($userInfo)) {
                throw new UserNotFoundException();
            }

            return $userInfo['hashed_password'];
        } catch (DbQueryException $ex) {
            throw new UserStoreException('Ошибка получения пользователя из базы данных', 0, $ex);
        }
    }

    /**
     * @return User[]
     * @throws UserStoreException
     */
    public function getAllUsers(): array {
        try {
            $dbUsers = $this->db->query(
                    "
                        SELECT id, login, hashed_password, email, name, last_name, 
                               patronymic, is_admin, position, salary, path_to_avatar, phone
                        FROM users 
                        "
            );

            $users = array();
            while ($userInfo = $dbUsers->fetch()) {
                $user = new User();

                $user->id = $userInfo['id'];
                $user->email = $userInfo['email'];
                $user->login = $userInfo['login'];
                $user->name = $userInfo['name'];
                $user->lastName = $userInfo['last_name'];
                $user->patronymic = $userInfo['patronymic'];
                $user->isAdmin = $userInfo['is_admin'] == 1;
                $user->pathToAvatar = $userInfo['path_to_avatar'] ?: '/public/img/upic-user.svg';
                $user->position = $userInfo['position'];
                $user->salary = $userInfo['salary'];
                $user->phone = $userInfo['phone'] ?: '';

                $users[$user->id] = $user;
            }

            return $users;
        } catch (DbQueryException $ex) {
            throw new UserStoreException('Ошибка получения пользователей из базы данных', 0, $ex);
        }
    }

    /**
     * @throws UserStoreException
     */
    public function getUserById(int $id): User {
        try {
            $dbUser = $this->db->query(
                    "
                        SELECT id, login, hashed_password, email, name, last_name, 
                               patronymic, is_admin, position, salary, path_to_avatar, phone
                        FROM users 
                        WHERE id = :id
                        ",
                    array('id' => $id)
            );
            $userInfo = $dbUser->fetch();

            if (empty($userInfo)) {
                throw new UserNotFoundException();
            }
            $user = new User();

            $user->id = $userInfo['id'];
            $user->email = $userInfo['email'];
            $user->login = $userInfo['login'];
            $user->name = $userInfo['name'];
            $user->lastName = $userInfo['last_name'];
            $user->patronymic = $userInfo['patronymic'];
            $user->isAdmin = $userInfo['is_admin'] == 1;
            $user->pathToAvatar = $userInfo['path_to_avatar'] ?: '/public/img/upic-user.svg';
            $user->position = $userInfo['position'];
            $user->salary = $userInfo['salary'];
            $user->phone = $userInfo['phone'] ?: '';
            return $user;
        } catch (DbQueryException $ex) {
            throw new UserStoreException('Ошибка получения пользователя из базы данных', 0, $ex);
        }
    }

    /**
     * @throws UserStoreException
     */
    public function updateUser(User $user, ?string $password = null): void {
        $passwordSqlPart = '';
        if (!empty($password)) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $passwordSqlPart = ", hashed_password = :password";
        }

        $sql = "
            UPDATE users SET name = :name, last_name = :last_name, patronymic = :patronymic,
                email = :email, phone = :phone, path_to_avatar = :path_to_avatar, position = :position,
                salary = :salary $passwordSqlPart
            WHERE id = :id
        ";

        $fields = array(
                'id' => $user->id,
                'name' => $user->name,
                'last_name' => $user->lastName,
                'patronymic' => $user->patronymic,
                'email' => $user->email,
                'phone' => $user->phone,
                'path_to_avatar' => $user->pathToAvatar,
                'position' => $user->position,
                'salary' => $user->salary,
        );

        if (!empty($password)) {
            $fields['password'] = $password;
        }

        try {
            $this->db->query($sql, $fields);
        } catch (DbQueryException $e) {
            throw new UserStoreException('Ошибка обновления пользователя в базе данных', 0, $e);
        }
    }

    /**
     * @throws UserStoreException
     */
    public function deleteUser(int $userId): void {
        try {
            $this->db->query(
                    "
                    DELETE FROM users WHERE id = :id
                ",
                    array(
                            'id' => $userId
                    )
            );
        } catch (DbQueryException $e) {
            throw new UserStoreException('Ошибка удаления пользователя из базы данных', 0, $e);
        }
    }

    /**
     * @throws UserStoreException
     */
    public function addUser(User $user, ?string $password = null): int {
        $passwordSqlPart = '';
        if (!empty($password)) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $passwordSqlPart = ", hashed_password = :password";
        }

        $sql = "
            INSERT INTO users SET name = :name, login = :login, last_name = :last_name, patronymic = :patronymic,
                email = :email, phone = :phone, path_to_avatar = :path_to_avatar, position = :position,
                salary = :salary $passwordSqlPart
        ";

        $fields = array(
                'login' => $user->login,
                'name' => $user->name,
                'last_name' => $user->lastName,
                'patronymic' => $user->patronymic,
                'email' => $user->email,
                'phone' => $user->phone,
                'path_to_avatar' => $user->pathToAvatar,
                'position' => $user->position,
                'salary' => $user->salary,
        );

        if (!empty($password)) {
            $fields['password'] = $password;
        }

        try {
            $this->db->query($sql, $fields);
            return $this->db->lastInsertId();
        } catch (DbQueryException $e) {
            var_dump($e);
            throw new UserStoreException('Ошибка добавления пользователя в базу данных', 0, $e);
        }
    }
}