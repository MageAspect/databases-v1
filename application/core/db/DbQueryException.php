<?php


namespace application\core\db;


use Exception;
use Throwable;


/**
 * Выбрасывается при ошибке запроса в бд
 */
class DbQueryException extends Exception {
}