<?php


namespace application\module\user\entity;


class User {
    public int $id;
    public string $login;
    public string $email;
    public string $hashedPassword;
    public string $name;
    public string $lastName;
    public string $patronymic;
    public string $isAdmin;
}