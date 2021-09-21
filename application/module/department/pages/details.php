<?php

/**
 * @var array $PAGE_DATA
 * @var string $PAGE_TITLE
 * @var Department $department
 */

use application\module\department\Department;


$department = $PAGE_DATA['department'];
$errors = $PAGE_DATA['errors'] ?? array();
?>

<div class="application-content">
    <div class="main-content-wrapper ui-scroll">
        <div class="main-content-header">
            <div class="main-content-header-title">
                <div class="main-content-header-title_icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="main-content-header-title_desc"><?= $PAGE_TITLE ?></div>
            </div>
            <div class="main-content-header-buttons">
                <button class="button button-back button-second" onclick="location = '/departments/'">
                    <span>К списку</span>
                </button>
                <?php if (empty($errors)): ?>
                <button class="button" onclick="location='/departments/<?= $department->id ?>/edit'">
                    <span>Редактировать</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="department-details">
            <?php if (empty($errors)): ?>
            <div class="department-details-block department-head-container">
                <div class="department-details-block-title">Руководитель отдела</div>
                <div class="main-user-preview-ext">
                    <div class="main-user-preview">
                        <div class="main-user-preview-avatar-wrapper">
                            <div class="main-user-preview-avatar center-background"
                                 style="background-image: url('<?= $department->head->pathToAvatar ?>');"></div>
                        </div>
                        <div class="main-user-preview-info">
                            <a href="/users/<?= $department->head->id ?>/details" class="main-user-preview-info_name a-hover"><?= $department->head->lastName ?> <?= $department->head->name ?></a>
                            <div class="main-user-preview-info_position"><?= $department->head->position ?></div>
                        </div>
                    </div>
                    <div class="main-user-preview-ext-fields">
                        <div class="main-user-preview-ext-fields-phone">Телефон:</div>
                        <div class="main-user-preview-ext-fields-email">Email:</div>
                    </div>
                    <div class="main-user-preview-ext-values">
                        <div class="main-user-preview-ext-values-phone"><?= $department->head->phone?></div>
                        <div class="main-user-preview-ext-values-email"><?= $department->head->email?></div>
                    </div>
                </div>
            </div>
            <div class="department-details-block">
                <div class="department-details-block-title">Описание</div>
                <div class="department-description"><?= $department->description ?></div>
            </div>
            <div class="department-details-block employees-container">
                <div class="department-details-block-title">Сотрудники (<?= count($department->members)?>)</div>
                <div class="employees-list">
                    <?php foreach ($department->members as $member): ?>
                        <div class="main-user-preview-ext">
                            <div class="main-user-preview">
                                <div class="main-user-preview-avatar-wrapper">
                                    <div class="main-user-preview-avatar center-background"
                                         style="background-image: url('<?= $member->pathToAvatar ?>');"></div>
                                </div>
                                <div class="main-user-preview-info">
                                    <a href="/users/<?= $member->id ?>/details" class="main-user-preview-info_name a-hover">
                                        <?= $member->lastName ?> <?= $member->name ?>
                                    </a>
                                    <div class="main-user-preview-info_position"><?= $member->position ?></div>
                                </div>
                            </div>
                            <div class="main-user-preview-ext-fields">
                                <div class="main-user-preview-ext-fields-phone">Телефон:</div>
                                <div class="main-user-preview-ext-fields-email">Email:</div>
                            </div>
                            <div class="main-user-preview-ext-values">
                                <div class="main-user-preview-ext-values-phone"><?= $member->phone?></div>
                                <div class="main-user-preview-ext-values-email"><?= $member->email?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="department-details-block">
                <div class="department-description">
                    <?=array_shift($errors) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

