<?php
/** @var array $PAGE_DATA */
/** @var string $PAGE_TITLE */

/** @var User $user */

use application\module\department\Department;
use application\module\user\entity\JournalEntry;
use application\module\user\entity\User;


$user = $PAGE_DATA['user'];
/** @var Department[] $userDepartments */
$userDepartments = $PAGE_DATA['user-departments'];
/** @var JournalEntry[] $userCarrierJournal */
$userCarrierJournal = $PAGE_DATA['user-carrier-journal'];
$errors = $PAGE_DATA['errors'];
$canReadSecurityFields = $PAGE_DATA['can-read-security-fields'];
?>

<div class="application-content">
    <div class="main-content-wrapper ui-scroll">
        <div class="main-content-header">
            <div class="main-content-header-title">
                <div class="main-content-header-title_icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="main-content-header-title_desc"><?= $PAGE_TITLE ?></div>
            </div>
            <div class="main-content-header-buttons">
                <button class="button button-back button-second" onclick="location = '/users/'">
                    <span>К списку</span>
                </button>
                <?php if ($canReadSecurityFields): ?>
                    <button class="button" onclick="location = '/users/<?= $user->id ?>/edit'">
                        <span>Редактировать</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="user-profile">
            <?php if (empty($errors)): ?>
                <div class="user-profile-additional-info">
                    <div class="user-profile-additional-info-avatar-container">
                        <div class="user-profile-additional-info-avatar">
                            <div class="user-profile-additional-info-avatar-center">
                                <div class="user-profile-additional-info-avatar"
                                     style="background-image: url('<?= $user->pathToAvatar ?>');">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="user-profile-additional-info-block">
                        <div class="user-profile-additional-info-block-title">Состоит в отделах:</div>
                        <div class="user-profile-additional-info-block-fields ">
                            <div class="departments-list user-profile-departments ui-scroll">
                                <?php foreach ($userDepartments as $department): ?>
                                    <div class="departments-list-item">
                                        <a href="/"
                                           class="a-hover departments-list-item_name"><?= $department->name ?></a>
                                        <div class="departments-list-item_head">
                                            <div class="main-user-preview">
                                                <div class="main-user-preview-avatar-wrapper">
                                                    <div class="main-user-preview-avatar center-background"
                                                         style="background-image: url('<?= $department->head->pathToAvatar ?>');"></div>
                                                </div>
                                                <div class="main-user-preview-info">
                                                    <a href="/" class="main-user-preview-info_name a-hover">
                                                        <?= $department->head->lastName ?> <?= $department->head->name ?>
                                                    </a>
                                                    <div class="main-user-preview-info_position"><?= $department->head->position ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="user-profile-contact-info">
                    <div class="user-profile-contact-info-block">
                        <div class="user-profile-contact-info-block-title">Основная информация</div>
                        <div class="user-profile-contact-info-block-fields">
                            <div class="user-profile-contact-info-block-fields-item">
                                <div class="user-profile-contact-info-block-fields-item-title">Фамилия:</div>
                                <div class="user-profile-contact-info-block-fields-item-value"><?= $user->lastName ?></div>
                            </div>
                            <div class="user-profile-contact-info-block-fields-item">
                                <div class="user-profile-contact-info-block-fields-item-title">Имя:</div>
                                <div class="user-profile-contact-info-block-fields-item-value"><?= $user->name ?></div>
                            </div>
                            <div class="user-profile-contact-info-block-fields-item">
                                <div class="user-profile-contact-info-block-fields-item-title">Отчество:</div>
                                <div class="user-profile-contact-info-block-fields-item-value"><?= $user->patronymic ?></div>
                            </div>
                            <div class="user-profile-contact-info-block-fields-item">
                                <div class="user-profile-contact-info-block-fields-item-title">Должность:</div>
                                <div class="user-profile-contact-info-block-fields-item-value"><?= $user->position ?></div>
                            </div>
                            <?php if ($canReadSecurityFields): ?>
                                <div class="user-profile-contact-info-block-fields-item">
                                    <div class="user-profile-contact-info-block-fields-item-title">Оклад:</div>
                                    <div class="user-profile-contact-info-block-fields-item-value">
                                        <?= number_format($user->salary) ?> рублей
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="user-profile-contact-info-block-fields-item">
                                <div class="user-profile-contact-info-block-fields-item-title">Email:</div>
                                <div class="user-profile-contact-info-block-fields-item-value"><?= $user->email ?></div>
                            </div>
                            <div class="user-profile-contact-info-block-fields-item">
                                <div class="user-profile-contact-info-block-fields-item-title">Телефон:</div>
                                <div class="user-profile-contact-info-block-fields-item-value"><?= $user->phone ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="user-profile-contact-info-block">
                        <div class="user-profile-contact-info-block-title">Журнал продвижения по службе</div>
                        <div class="user-profile-contact-info-block-fields">
                            <div class="main-grid-table user-carrier-journal ui-scroll"
                                    <?= !$canReadSecurityFields ? 'style="grid-template-columns: repeat(5, min-content);"' : '' ?>
                            >
                                <div class="main-grid-table-row main-grid-table-row-head">
                                    <div class="main-grid-table-row-column">Должность</div>
                                    <div class="main-grid-table-row-column">Отдел</div>
                                    <?php if ($canReadSecurityFields): ?>
                                        <div class="main-grid-table-row-column">Оклад</div>
                                    <?php endif; ?>
                                    <div class="main-grid-table-row-column">Срок в должности</div>
                                    <div class="main-grid-table-row-column">Дата вступления</div>
                                    <div class="main-grid-table-row-column">Дата выхода из должности</div>
                                </div>
                                <?php foreach ($userCarrierJournal as $journalEntry): ?>
                                    <div class="main-grid-table-row">
                                        <div class="main-grid-table-row-column"><?= $journalEntry->position ?></div>
                                        <div class="main-grid-table-row-column">
                                            <?= $journalEntry->department ? $journalEntry->department->name : '—' ?>
                                        </div>
                                        <?php if ($canReadSecurityFields): ?>
                                            <div class="main-grid-table-row-column">
                                                <?= number_format($journalEntry->salary) ?> рублей
                                            </div>
                                        <?php endif; ?>
                                        <div class="main-grid-table-row-column">
                                            <?= $journalEntry->daysInWork ?> дней
                                        </div>
                                        <div class="main-grid-table-row-column"><?= $journalEntry->startDateTime->format('d.m.Y') ?></div>
                                        <div class="main-grid-table-row-column">
                                            <?= $journalEntry->endDateTime ? $journalEntry->endDateTime->format('d.m.Y') : 'До сих пор в должности' ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="application-content-error"><?= array_shift($errors) ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>
