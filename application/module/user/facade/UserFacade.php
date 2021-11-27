<?php


namespace application\module\user\facade;


use application\module\user\control\exception\UserNotFoundException;
use application\module\user\control\exception\UserSessionServiceException;
use application\module\user\control\UserSessionService;
use application\module\user\control\UserStore;
use application\module\user\control\exception\UserStoreException;
use application\module\user\entity\User;


class UserFacade {
    private UserSessionService $userService;
    private UserStore $userStore;

    public function __construct(?UserSessionService $userService = null, ?UserStore $userStore = null) {
        $this->userService = $userService ?? new UserSessionService();
        $this->userStore = $userStore ?? new UserStore();
    }

    /**
     * @throws UserFacadeException
     */
    public function authUser(string $login, string $password): void {
        $login = trim($login);

        if (empty($login)) {
            throw new UserFacadeException('Не задан логин');
        }

        if (empty($password)) {
            throw new UserFacadeException('Не задан пароль');
        }

        try {
            $user = $this->userStore->getUserByLogin($login);
            $hashedPass = $this->userStore->getUserHashedPassword($user->id);
        }
        catch (UserNotFoundException $e) {
            throw new UserFacadeException('Пользователь с логином ' . $login . ' не найден!', 0, $e);
        }
        catch (UserStoreException $e) {
            throw new UserFacadeException('Ошибка сервера попробуйте позже!', 0, $e);
        }

        $isCorrectPassword = password_verify($password, $hashedPass);

        if (!$isCorrectPassword) {
            throw new UserFacadeException('Неверный пароль');
        }

        try {
            $this->userService->setUser($user);
        } catch (UserSessionServiceException $e) {
            throw new UserFacadeException('Не удалось авторизовать пользователя', 0, $e);
        }
    }

    /**
     * @throws UserFacadeException
     */
    public function getCurrentUser(): User {
        try {
            $sessionUser = $this->userService->getUser();
            return $this->userStore->getUserById($sessionUser->id);
        } catch (\Exception $e) {
            throw new UserFacadeException('Не удалось получить текущего пользователя');
        }
    }

    /**
     * @throws UserFacadeException
     */
    public function getAllUsers(): array {
        try {
            return $this->userStore->getAllUsers();
        } catch (UserStoreException $e) {
            throw new UserFacadeException('Не удалось получить текущего пользователя');
        }
    }

    /**
     * @throws UserFacadeException
     */
    public function getUserById(int $id): User {
        try {
            return $this->userStore->getUserById($id);
        } catch (UserStoreException $e) {
            throw new UserFacadeException('Не удалось получить пользователя');
        }
    }

    /**
     * @throws UserFacadeException
     */
    public function getUserByLogin(string $login): User {
        try {
            return $this->userStore->getUserByLogin($login);
        } catch (UserNotFoundException $e) {
            throw new UserNotFoundFacadeException();
        } catch (UserStoreException $e) {
            throw new UserFacadeException('Не удалось получить пользователя');
        }
    }

    /**
     * @throws UserFacadeException
     */
    public function updateUser(User $user, ?string $password = null): void {
        try {
            $this->userStore->updateUser($user, $password);
        } catch (UserStoreException $e) {
            throw new UserFacadeException('Не удалось обновить пользователя');
        }
    }

    /**
     * @throws UserFacadeException
     */
    public function deleteUser(int $userId): void {
        try {
            $this->userStore->deleteUser($userId);
        } catch (UserStoreException $e) {
            throw new UserFacadeException('Не удалось удалить пользователя');
        }
    }

    /**
     * @throws UserFacadeException
     */
    public function addUser(User $user, string $password): int {
        try {
            return $this->userStore->addUser($user, $password);
        } catch (UserStoreException $e) {
            throw new UserFacadeException('Не удалось добавить пользователя');
        }
    }

    /**
     * @throws UserFacadeException
     */
    public function isAuthorisedUser(): bool {
        try {
            return $this->userService->isAuthorised();
        } catch (UserSessionServiceException $e) {
            throw new UserFacadeException('Не удалось получить информацию об авторизации пользователя');
        }
    }

    /**
     * @throws UserFacadeException
     */
    public function logOutUser(): void {
        try {
            $this->userService->logOutUser();
        } catch (UserSessionServiceException $e) {
            throw new UserFacadeException('Не удалось выйти');
        }
    }
}