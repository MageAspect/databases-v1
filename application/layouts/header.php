<?php
/** @var $title */
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap&subset=cyrillic" rel="stylesheet">
    <link rel="stylesheet" href="/public/assets/css/main.css">
    <link rel="icon" href="/public/assets/img/favicon.ico" type="image/x-icon">
    <title><?= htmlspecialchars($title, ENT_QUOTES) ?></title>
</head>
<body>
<div class="menu-wrapper">
    <div class="logo">
        <a href="/">INTERLABA</a>
    </div>
    <div class="menu">
        <div class="menu-items">
            <a href="/">Aвторизация</a>
            <a href="/form/">Обратная связь</a>
            <? if (!empty($user['login'])) : ?>
                <a href="/admin/">Админка</a>
            <? endif; ?>
        </div>

    </div>
    <div class="user"><?= !empty($user['login']) ? htmlspecialchars($user['login'], ENT_QUOTES) : 'Вход не выполнен' ?></div>
</div>