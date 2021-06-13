<?php


namespace application\module\user\control;


use application\module\user\control\exception\SessionServiceException;


class SessionService {

    /**
     * @throws SessionServiceException
     */
    public function set(string $key, $value): void {
        $this->start();
        $_SESSION[$key] = $value;
        $this->writeClose();
    }

    /**
     * @throws SessionServiceException
     */
    public function get(string $key) {
        $this->start();
        $res = $_SESSION[$key] ?? null;
        $this->writeClose();

        return $res;
    }

    /**
     * @throws SessionServiceException
     */
    public function unset($key) {
        $this->start();
        unset($_SESSION[$key]);
        $this->writeClose();
    }

    protected function start(): void {
        if (!$this->sessionExists()) {
            session_start();
        }
    }

    /**
     * @throws SessionServiceException
     */
    protected function writeClose(): void {
        if ($this->sessionExists() && !session_write_close()) {
            throw new SessionServiceException('Ошибка записи в сессию');
        }
    }



    protected function sessionExists(): bool {
        return session_status() != PHP_SESSION_NONE;
    }
}