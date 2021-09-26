<?php

namespace application\module\user\entity;


use application\module\department\Department;
use DateTime;


class JournalEntry {
    public int $id;
    public int $userId;
    public ?Department $department = null;
    public string $position;
    public string $salary;
    public int $daysInWork;
    public DateTime $startDateTime;
    public ?DateTime $endDateTime = null;
}