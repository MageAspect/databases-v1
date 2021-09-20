<?php

/** @var array $PAGE_DATA */

use application\module\user\facade\UserFacade;

$user = (new UserFacade())->getCurrentUser();
?>

<div class="application-sidebar">
    <div class="user">
        <div class="user-avatar-wrapper">
            <div class="user-avatar center-background"
                 style="background-image: url('/assets/img/pexels-pixabay-220453.jpg');"></div>
        </div>
        <div class="user-info">
            <a href="/profile/" class="user-info-name a-hover">
                Марк Прохоров
            </a>
        </div>
    </div>
    <div class="menu-wrapper">
        <div class="menu">
            <div class="menu-desc">Главное меню</div>
            <a href="/" class="menu-item">
                <div class="menu-item-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="menu-item-link">Отделы</div>
            </a>
            <a href="/" class="menu-item <!--menu-item__selected-->">
                <div class="menu-item-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="menu-item-link">Сотрудники</div>
            </a>
            <?php if ($user->isAdmin): ?>
                <a href="/" class="menu-item menu-item__selected">
                    <div class="menu-item-icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div class="menu-item-link">SQL запрос</div>
                </a>
            <?php endif; ?>
        </div>
        <div class="menu">
            <div class="menu-desc">Управление аккаунтом</div>
            <a href="/" class="menu-item">
                <div class="menu-item-icon">
                    <i class="fas fa-user-cog"></i>
                </div>
                <div class="menu-item-link">Настройки</div>
            </a>
            <a href="/" class="menu-item">
                <div class="menu-item-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <div class="menu-item-link">Выйти</div>
            </a>
        </div>
    </div>
</div>