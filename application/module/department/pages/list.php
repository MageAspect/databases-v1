<?php
/**
 * @var array $PAGE_DATA
 */

/** @var Department[] $departments */

use application\module\department\Department;
use application\module\user\entity\User;


$departments = $PAGE_DATA['departments'];
$userPerms = $PAGE_DATA['user-perms'];
/** @var User $currenUser */
$currenUser = $PAGE_DATA['current-user'];
$errors = $PAGE_DATA['errors'];
?>


<div class="application-content">
    <div class="main-content-wrapper departments-list-wrapper ui-scroll">
        <div class="main-content-header departments-list-header">
            <div class="main-content-header-title">
                <div class="main-content-header-title_icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="main-content-header-title_desc">Список отделов</div>
            </div>
            <?php if ($currenUser->isAdmin): ?>
                <button class="main-content-header-button button" onclick="location = '/departments/0/edit'">
                    <i class="fas fa-plus"></i> <span>Добавить</span>
                </button>
            <?php endif ?>
        </div>
        <div class="departments-list">
            <?php if (empty($errors)): ?>
                <?php foreach ($departments as $department): ?>
                    <div class="departments-list-item">
                        <a href="/departments/<?= $department->id ?>/details/"
                           class="a-hover departments-list-item_name"><?= $department->name ?></a>
                        <div class="departments-list-item_head">
                            <div class="main-user-preview">
                                <div class="main-user-preview-avatar-wrapper">
                                    <div class="main-user-preview-avatar center-background"
                                         style="background-image: url('<?= $department->head->pathToAvatar ?>');"></div>
                                </div>
                                <div class="main-user-preview-info">
                                    <a href="/users/<?= $department->head->id ?>/details"
                                       class="main-user-preview-info_name a-hover"><?= $department->head->lastName ?> <?= $department->head->name ?></a>
                                    <div class="main-user-preview-info_position"><?= $department->head->position ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="departments-list-item_desc"><?= $department->description ?></div>
                        <?php if ($userPerms[$department->id]['is-user-admin'] || $userPerms[$department->id]['is-user-department-head']): ?>
                            <div class="departments-list-item_buttons">
                                <a href="/departments/<?= $department->id ?>/edit/"
                                   class="button button-edit button-no-shadow">редактировать</a>
                                <?php if ($userPerms[$department->id]['is-user-admin']): ?>
                                    <a href="/departments/<?= $department->id ?>/delete/"
                                       class="button button-delete button-no-shadow">Удалить</i></a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="application-content-error"><?= array_shift($errors) ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>
