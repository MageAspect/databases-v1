<?php


use application\core\Application;


spl_autoload_register(function ($class) {
    $classPath = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] . '/' . $class . '.php');
    if (file_exists($classPath)) {
        include $classPath;
    }
});

try {
    Application::getInstance()->run();
} catch (Exception $e) {
    echo 'Ошибка сервера ' . $e->getMessage() . 'Строка: ' . $e->getLine() . ' ' . $e->getFile();
}





