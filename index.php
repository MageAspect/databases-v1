<?php


use application\core\Application;


error_reporting(E_ALL);
ini_set('display_errors', 1);

spl_autoload_register(
        function ($class) {
            $classPath = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] . '/' . $class . '.php');
            if (file_exists($classPath)) {
                include $classPath;
            }
        }
);

try {
    Application::getInstance()->run();
} catch (Exception $e) {
    echo 'Ошибка сервера ' . $e->getMessage() . 'Строка: ' . $e->getLine() . ' ' . $e->getFile();
}





