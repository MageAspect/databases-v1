<?php

/**
 * @author Mark Prohorov <mark@intervolga.ru>
 */


namespace application\module\department;


use application\module\user\entity\User;


class Department {
    public int $id;
    public string $name;
    public string $description;
    public ?User $head;

    /**
     * @var User[] array
     */
    public array $members = array();
}