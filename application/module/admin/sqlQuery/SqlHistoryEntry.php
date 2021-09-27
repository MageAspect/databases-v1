<?php

namespace application\module\admin\sqlQuery;


use DateTime;


class SqlHistoryEntry {
    public int $id;
    public string $sql;
    public DateTime $executionDateTime;
}