<?php
/** @var User[] $allUsers */
/** @var array $PAGE_DATA */

/** @var User $currentUser */

use application\module\user\entity\User;


$allUsers = $PAGE_DATA['users'];
$currentUser = $PAGE_DATA['current-user'];
?>

<div class="application-content">
    <div class="main-content-wrapper ui-scroll">
        <div class="main-content-header">
            <div class="main-content-header-title">
                <div class="main-content-header-title_icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="main-content-header-title_desc">Список сотрудников</div>
            </div>
            <?php if ($currentUser->isAdmin): ?>
                <button class="main-content-header-button button" onclick="location = '/users/0/edit'">
                    <i class="fas fa-plus"></i><span>Добавить</span>
                </button>
            <?php endif; ?>
        </div>
        <div class="main-grid-table users-list">
            <div class="main-grid-table-row main-grid-table-row-head">
                <div class="main-grid-table-row-column"></div>
                <div class="main-grid-table-row-column">id</div>
                <div class="main-grid-table-row-column">Фамилия</div>
                <div class="main-grid-table-row-column">Имя</div>
                <div class="main-grid-table-row-column">Отчество</div>
                <div class="main-grid-table-row-column">email</div>
                <div class="main-grid-table-row-column">Должность</div>
                <div class="main-grid-table-row-column">Телефон</div>
            </div>
            <?php foreach ($allUsers as $user): ?>
                <div class="main-grid-table-row">
                    <div class="main-grid-table-row-column main-grid-table-row-actions">
                        <i class="fas fa-bars"></i>
                        <div class="main-grid-table-row-actions-container" style="display: none">
                                <div class="main-grid-table-row-actions-item main-grid-table-row-actions-item-view"
                                     onclick="location = '/users/<?= $user->id ?>/details'">
                                    Посмотреть
                                </div>
                            <?php if ($currentUser->isAdmin): ?>
                                <div class="main-grid-table-row-actions-item main-grid-table-row-actions-item-edit"
                                     onclick="location = '/users/<?= $user->id ?>/edit'">
                                    Изменить
                                </div>
                                <div class="main-grid-table-row-actions-item main-grid-table-row-actions-item-delete"
                                     onclick="location = '/users/<?= $user->id ?>/delete'">
                                    Удалить
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="main-grid-table-row-column"><?= $user->id ?></div>
                    <div class="main-grid-table-row-column"><?= $user->lastName ?></div>
                    <div class="main-grid-table-row-column"><?= $user->name ?></div>
                    <div class="main-grid-table-row-column"><?= $user->patronymic ?></div>
                    <div class="main-grid-table-row-column"><?= $user->email ?></div>
                    <div class="main-grid-table-row-column"><?= $user->position ?></div>
                    <div class="main-grid-table-row-column"><?= $user->phone ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    let actionButtonsContainers = document.querySelectorAll('.main-grid-table-row-actions');

    for (let container of actionButtonsContainers) {
        container.addEventListener('click', () => {
            for (let container of actionButtonsContainers) {
                container.querySelector('.main-grid-table-row-actions-container').style.display = 'none';
            }
            container.querySelector('.main-grid-table-row-actions-container').style.display = 'block';

        })
    }

    window.addEventListener('click', (e) => {
        for (let container of actionButtonsContainers) {
            if (container.parentElement.contains(e.target)) {
                return;
            }
            container.querySelector('.main-grid-table-row-actions-container').style.display = 'none';
        }
    })
</script>
