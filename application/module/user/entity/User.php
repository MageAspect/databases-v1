<?php


namespace application\module\user\entity;


class User {
    public int $id;
    public string $login;
    public string $email;
    public string $hashedPassword;
}