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
            $user->hashedPassword = $userInfo['hashed_password'];
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
            $user->hashedPassword = $userInfo['hashed_password'];
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
}