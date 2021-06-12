<?php

/**
 * @author Mark Prohorov <mark@intervolga.ru>
 */


namespace application\module\user\control;


use application\module\user\entity\User;


class UserSessionService {
    private SessionService $sessionService;

    public function __construct(?SessionService $sessionService = null) {
        $this->sessionService = $sessionService ?: new SessionService();
    }

    /**
     * @throws SessionUserNotFoundException
     */
    public function getCurrent(): User {
        $sessionUser = $this->sessionService->get('user');
        if (empty($sessionUser)) {
            throw new SessionUserNotFoundException();
        }

        $user = new User();
        $user->id = $sessionUser['id'];
        $user->email = $sessionUser['email'];
        $user->login = $sessionUser['login'];
        $user->hashedPassword = $sessionUser['hashedPassword'];

        return $user;
    }

    public function isAuthorised(): bool {
        return !empty($this->sessionService->get('user'));
    }

    public function setCurrent(User $user): bool {
        return $this->sessionService->set(
                'user',
                array(
                        'id' => $user->id,
                        'login' => $user->login,
                        'email' => $user->email,
                        'hashedPassword' => $user->hashedPassword,
                )
        );
    }
}