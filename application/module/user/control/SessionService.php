<?php


namespace application\module\user\control;


class SessionService {

    public function set($key, $value): bool {
        $this->start();
        $_SESSION[$key] = $value;
        return $this->writeClose();
    }

    public function get($key) {
        $this->start();
        $res = $_SESSION[$key];
        $this->writeClose();

        return $res;
    }

    public function unset($key) {
        $this->start();
        unset($_SESSION[$key]);
        $this->writeClose();
    }

    private function start() {
        if (!$this->sessionExists()) {
            session_start();
        }
    }

    private function writeClose(): bool {
        if ($this->sessionExists()) {
            return session_write_close();
        }
        return false;
    }

    private function sessionExists(): bool {
        return session_status() != PHP_SESSION_NONE;
    }
}