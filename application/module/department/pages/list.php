<?php
/**
 * @var array $PAGE_DATA
 */

/** @var Department[] $departments */

use application\module\department\Department;


$departments = $PAGE_DATA['departments'];
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
            <button class="main-content-header-button button">
                <i class="fas fa-plus"></i> <span>Добавить</span>
            </button>
        </div>
        <div class="departments-list">
            <?php foreach ($departments as $department): ?>
            <div class="departments-list-item">
                <a href="/departments/<?= $department->id?>/view/" class="a-hover departments-list-item_name"><?= $department->name ?></a>
                <div class="departments-list-item_head">
                    <div class="main-user-preview">
                        <div class="main-user-preview-avatar-wrapper">
                            <div class="main-user-preview-avatar center-background"
                                 style="background-image: url('<?= $department->head->pathToAvatar ?>');"></div>
                        </div>
                        <div class="main-user-preview-info">
                            <a href="/" class="main-user-preview-info_name a-hover"><?= $department->head->name ?> <?= $department->head->lastName ?></a>
                            <div class="main-user-preview-info_position"><?= $department->head->position ?></div>
                        </div>
                    </div>
                </div>
                <div class="departments-list-item_desc"><?= $department->description ?></div>
                <div class="departments-list-item_buttons">
                    <a href="/departments/<?= $department->id?>/edit/" class="button button-edit button-no-shadow">редактировать</a>
                    <a href="/departments/<?= $department->id?>/delete/" class="button button-delete button-no-shadow">Удалить</i></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>