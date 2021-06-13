<?php

/**
 * @author Mark Prohorov <mark@intervolga.ru>
 */


namespace application\module\user\control;


use application\module\user\control\exception\SessionServiceException;
use application\module\user\control\exception\SessionUserNotFoundException;
use application\module\user\control\exception\UserSessionServiceException;
use application\module\user\entity\User;


class UserSessionService {
    private SessionService $sessionService;

    public function __construct(?SessionService $sessionService = null) {
        $this->sessionService = $sessionService ?: new SessionService();
    }

    /**
     * @throws SessionUserNotFoundException
     * @throws UserSessionServiceException
     */
    public function getUser(): User {
        try {
            $sessionUser = $this->sessionService->get('user');
        } catch (SessionServiceException $e) {
            throw new UserSessionServiceException('Ошибка получения пользователя из сессии', 0, $e);
        }
        if (empty($sessionUser)) {
            throw new SessionUserNotFoundException();
        }

        return $sessionUser;
    }

    /**
     * @throws UserSessionServiceException
     */
    public function isAuthorised(): bool {
        try {
            return !empty($this->sessionService->get('user'));
        } catch (SessionServiceException $e) {
            throw new UserSessionServiceException('Ошибка получения информации об авторизованности пользователя', 0, $e);
        }
    }

    /**
     * @throws UserSessionServiceException
     */
    public function setUser(User $user): void {
        try {
            $this->sessionService->set(
                    'user',
                    $user
            );
        } catch (SessionServiceException $e) {
            throw new UserSessionServiceException('Ошибка записи пользователя в сессию', 0, $e);
        }
    }
}