<?php


namespace application\module\user\facade;


use application\core\exception\ArgumentException;
use application\module\user\control\SessionUserNotFoundException;
use application\module\user\control\UserNotFoundException;
use application\module\user\control\UserSessionService;
use application\module\user\control\UserStore;
use application\module\user\control\UserStoreException;
use application\module\user\entity\User;


class UserFacade {
    private UserSessionService $userService;
    private UserStore $userStore;

    public function __construct(?UserSessionService $userService = null, ?UserStore $userStore = null) {
        $this->userService = $userService ?? new UserSessionService();
        $this->userStore = $userStore ?? new UserStore();
    }

    /**
     * @throws ArgumentException
     * @throws IncorrectPasswordException
     * @throws UserFacadeException
     * @throws UserNotFoundException
     */
    public function authUser(string $login, string $password): bool {
        $login = trim($login);

        if (empty($login)) {
            throw new ArgumentException('Не задан login');
        }

        if (empty($password)) {
            throw new ArgumentException('Не задан password');
        }

        try {
            $user = $this->userStore->getUserByLogin($login);
        } catch (UserStoreException $e) {
            throw new UserFacadeException('Ошибка получения пользователя из базы данных!', 0, $e);
        } catch (UserNotFoundException $e) {
            throw new UserNotFoundException('Пользователь с логином ' . $login . ' не найден!', 0, $e);
        }

        $isCorrectPassword = password_verify($password, $user->hashedPassword);

        if (!$isCorrectPassword) {
            throw new IncorrectPasswordException('Неверный пароль для пользователя ' . $login);
        }

        return $this->userService->setCurrent($user);
    }

    /**
     * @throws SessionUserNotFoundException
     */
    public function getUser(): User {
        return $this->userService->getCurrent();
    }

    public function isAuthorisedUser(): bool {
        return $this->userService->isAuthorised();
    }
}