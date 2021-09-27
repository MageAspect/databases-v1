<?php


namespace application\module\user\entity;


class User {
    public int $id = 0;
    public string $login = '';
    public string $email = '';
    public string $name = '';
    public string $lastName = '';
    public string $patronymic = '';
    public string $pathToAvatar = '';
    public string $position = '';
    public int $salary = 0;
    public bool $isAdmin = false;
    public string $phone = '';
}