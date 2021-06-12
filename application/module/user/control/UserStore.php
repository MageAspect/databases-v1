<?php

/**
 * @author Mark Prohorov <mark@intervolga.ru>
 */


namespace application\module\user\control;


use application\core\Db;
use application\core\exception\DbQueryException;
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
                    "SELECT id, login, email, `password` FROM users WHERE login = :login",
                    array('login' => $login)
            );
            $userInfo = $dbUser->fetch();

            if (empty($userInfo)) {
                throw new UserNotFoundException();
            }
            $user = new User();

            $user->id = $userInfo['id'];
            $user->email = $userInfo['email'];
            $user->hashedPassword = $userInfo['password'];
            $user->login = $userInfo['login'];
            return $user;
        } catch (DbQueryException $ex) {
            throw new UserStoreException('Ошибка получения пользователя из базы данных', 0, $ex);
        }
    }
}