<?php

/**
 * @var array $PAGE_DATA
 * @var string $PAGE_TITLE
 * @var Department $department
 */

use application\module\department\Department;
use application\module\user\entity\User;


$department = $PAGE_DATA['department'];
$errors = $PAGE_DATA['errors'] ?? array();
$validationErrors = $PAGE_DATA['validation-errors'] ?? array();

/** @var User[] $availableMembers */
$availableMembers = $PAGE_DATA['available-members'] ?? array();

$isUserAdmin = $PAGE_DATA['is-user-admin']
?>

<div class="application-content">
    <div class="main-content-wrapper ui-scroll">
        <div class="main-content-header">
            <div class="main-content-header-title">
                <div class="main-content-header-title_icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <?php if ($isUserAdmin): ?>
                    <div class="main-content-header-title_desc">Редактирование: <input type="text"
                                                                                       class="department-title-edit"
                                                                                       value="<?= $department->name ?>">
                    </div>
                <?php else: ?>
                    <div class="main-content-header-title_desc">Редактирование: <?= $department->name ?></div>
                <?php endif; ?>
            </div>
            <div class="main-content-header-buttons">
                <button class="button button-back button-second" onclick="location = '/departments/'">
                    <span>К списку</span>
                </button>
                <button class="button button-save" id="save-department">
                    <span>Сохранить</span>
                </button>
            </div>
        </div>
        <form class="department-details department-edit" id="department-form" method="post">
            <?php if (empty($errors)): ?>

                <input type="hidden" name="head-id" value="<?= $department->head->id ?>">
                <input type="hidden" name="title" value="<?= $department->name ?>">
                <input type="hidden" name="members-ids"
                       value="<?= implode(',', array_map(fn(User $u) => $u->id, $department->members)) ?>">
                <div class="department-details-block department-head-container">
                    <div class="department-details-block-title department-edit-block-title">
                        <div class="department-details-block-title-desc">Руководитель отдела</div>
                        <?php if ($isUserAdmin): ?>
                            <div class="department-edit-block-title-buttons">
                                <button class="button button-second button-edit " id="edit-department-head">
                                    <span class="point-events">изменить</span>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="main-user-preview-ext" id="department-head-ext-container">
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
                        <div class="main-user-preview-ext-fields">
                            <div class="main-user-preview-ext-fields-phone">Телефон:</div>
                            <div class="main-user-preview-ext-fields-email">Email:</div>
                        </div>
                        <div class="main-user-preview-ext-values">
                            <div class="main-user-preview-ext-values-phone"><?= $department->head->phone ?></div>
                            <div class="main-user-preview-ext-values-email"><?= $department->head->email ?></div>
                        </div>
                    </div>
                </div>
                <div class="department-details-block">
                    <div class="department-details-block-title">Описание</div>
                    <textarea name="description"
                              class="department-description department-description-edit ui-scroll"><?= $department->description ?></textarea>
                </div>
                <div class="department-details-block department-edit-block employees-container">
                    <div class="department-details-block-title department-edit-block-title">
                        <div class="department-edit-block-title-desc">Сотрудники (<?= count($department->members) ?>)
                        </div>
                        <div class="department-edit-block-title-buttons">
                            <button class="button button-add button-second" id="add-department-member">
                                <i class="fas fa-plus point-events"></i> <span class="point-events">Добавить</span>
                            </button>
                        </div>
                    </div>
                    <div class="employees-list main-user-preview-ext-grid-list">
                        <?php foreach ($department->members as $member): ?>
                            <div class="main-user-preview-ext main-user-preview-ext-in-grid"
                                 data-user-id="<?= $member->id ?>">
                                <div class="main-user-preview">
                                    <div class="main-user-preview-avatar-wrapper">
                                        <div class="main-user-preview-avatar center-background"
                                             style="background-image: url('<?= $member->pathToAvatar ?>');"></div>
                                    </div>
                                    <div class="main-user-preview-info">
                                        <a href="/users/<?= $member->id ?>/details"
                                           class="main-user-preview-info_name a-hover">
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
                                    <div class="main-user-preview-ext-values-phone"><?= $member->phone ?></div>
                                    <div class="main-user-preview-ext-values-email"><?= $member->email ?></div>
                                </div>
                                <div class="main-user-preview-ext-actions">
                                    <button class="button button-second button-delete delete-member"
                                            data-member-id="<?= $member->id ?>">
                                        <span class="point-events">удалить</span>
                                    </button>
                                </div>
                            </div>
                            <div class="main-user-preview-ext-in-grid-line" data-user-id="<?= $member->id ?>"></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="application-content-error"><?= array_shift($errors) ?></div>
            <?php endif; ?>
        </form>
        <div class="user-selector-popup user-selector-popup-hidden">
            <div class="user-selector-container">
                <div class="user-selector ui-scroll main-user-preview-ext-grid-list">
                    <?php foreach ($availableMembers as $member): ?>
                        <div class="main-user-preview-ext main-user-preview-ext-in-grid">
                            <div class="main-user-preview">
                                <div class="main-user-preview-avatar-wrapper">
                                    <div class="main-user-preview-avatar center-background"
                                         style="background-image: url('<?= $member->pathToAvatar ?>');"></div>
                                </div>
                                <div class="main-user-preview-info">
                                    <a href="/users/<?= $member->id ?>/details"
                                       class="main-user-preview-info_name a-hover">
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
                                <div class="main-user-preview-ext-values-phone"><?= $member->phone ?></div>
                                <div class="main-user-preview-ext-values-email"><?= $member->email ?></div>
                            </div>
                            <div class="main-user-preview-ext-actions">
                                <button class="button button-second button-edit select-user-button"
                                        data-user-id="<?= $member->id ?>">
                                    <span class="point-events">выбрать</span>
                                </button>
                            </div>
                            <div></div>
                        </div>
                        <div class="main-user-preview-ext-in-grid-line"></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    class DepartmentForm {
        constructor(membersIds) {
            this.members = Object.keys(membersIds).length > 0 ? Object.values(membersIds) : [];
            this.form = document.querySelector('#department-form');
            this.userSelector = new UserSelector();
        }

        initEvents() {
            let saveButton = document.querySelector('#save-department');

            saveButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.submit();
            });

            let editHeadButton = document.querySelector('#edit-department-head');
            if (editHeadButton) {
                editHeadButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.userSelector.setCallBack(this.setDepartmentHead.bind(this))
                    this.userSelector.showPopup();
                });
            }


            let addMemberButton = document.querySelector('#add-department-member');
            addMemberButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.userSelector.setCallBack(this.addMember.bind(this))
                this.userSelector.showPopup();
            });

            this.initDeleteMemberButtons();
        }

        initDeleteMemberButtons() {
            let removeMemberButtons = document.querySelectorAll('.delete-member');

            for (let button of removeMemberButtons) {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.removeMember(e.target.dataset.memberId);
                })
            }
        }

        submit() {
            let titleInput = document.querySelector('.department-title-edit');
            if (titleInput) {
                this.form.querySelector('[name="title"]').value = titleInput.value
            }

            this.form.querySelector('[name="members-ids"]').value = this.members.join(',');
            this.form.submit();
        }

        async setDepartmentHead(id) {
            let user = await this.getJsonUser(id);

            let headHiddenInput = document.querySelector('[name="head-id"]')
            headHiddenInput.value = user.id;

            let headContainer = document.querySelector('#department-head-ext-container');
            headContainer.innerHTML = `
            <div class="main-user-preview">
                        <div class="main-user-preview-avatar-wrapper">
                            <div class="main-user-preview-avatar center-background"
                                 style="background-image: url('${user.pathToAvatar}');"></div>
                        </div>
                        <div class="main-user-preview-info">
                            <a href="/users/${user.id}/details" class="main-user-preview-info_name a-hover">${user.lastName} ${user.name}</a>
                            <div class="main-user-preview-info_position">${user.position}</div>
                        </div>
                    </div>
                    <div class="main-user-preview-ext-fields">
                        <div class="main-user-preview-ext-fields-phone">Телефон:</div>
                        <div class="main-user-preview-ext-fields-email">Email:</div>
                    </div>
                    <div class="main-user-preview-ext-values">
                        <div class="main-user-preview-ext-values-phone">${user.phone}</div>
                        <div class="main-user-preview-ext-values-email">${user.email}</div>
                    </div>
            `;
        }

        async getJsonUser(id) {
            let response = await fetch('/api/1/user/' + id);

            let result = (await response).json();

            if (result.error) {
                console.log('Ошибка получения пользвоателя с id ' + id)
            }

            return result;
        }

        async addMember(id) {
            if (this.members.includes(id)) {
                return;
            }
            this.members.push(id);
            let user = await this.getJsonUser(id);
            let memberHtml = `
                    <div class="main-user-preview-ext main-user-preview-ext-in-grid" data-user-id="${user.id}">
                        <div class="main-user-preview">
                            <div class="main-user-preview-avatar-wrapper">
                                <div class="main-user-preview-avatar center-background"
                                     style="background-image: url('${user.pathToAvatar}');"></div>
                            </div>
                            <div class="main-user-preview-info">
                                <a href="/users/${user.id}/details" class="main-user-preview-info_name a-hover">
                                    ${user.lastName} ${user.name}
                                </a>
                                <div class="main-user-preview-info_position">${user.position}</div>
                            </div>
                        </div>
                        <div class="main-user-preview-ext-fields">
                            <div class="main-user-preview-ext-fields-phone">Телефон:</div>
                            <div class="main-user-preview-ext-fields-email">Email:</div>
                        </div>
                        <div class="main-user-preview-ext-values">
                            <div class="main-user-preview-ext-values-phone">${user.phone}</div>
                            <div class="main-user-preview-ext-values-email">${user.email}</div>
                        </div>
                        <div class="main-user-preview-ext-actions">
                            <button class="button button-second button-delete delete-member" data-member-id="${user.id}">
                                <span class="point-events">удалить</span>
                            </button>
                        </div>
                    </div>
                    <div class="main-user-preview-ext-in-grid-line" data-user-id="${user.id}"></div>
            `;

            let membersList = document.querySelector('.employees-list');

            membersList.innerHTML += memberHtml;

            this.initDeleteMemberButtons();
        }

        removeMember(id) {
            this.members = this.members.filter(value => value != id);

            let member = document.querySelector(`.main-user-preview-ext-in-grid[data-user-id="${id}"]`);
            let memberBorder = document.querySelector(`.main-user-preview-ext-in-grid-line[data-user-id="${id}"]`);
            member.remove();
            memberBorder.remove();
        }
    }

    class UserSelector {
        constructor() {
            this.selectionCallBack = null;
            this.userListPopup = document.querySelector('.user-selector-popup');

            for (let selectButton of this.userListPopup.querySelectorAll('.select-user-button')) {
                selectButton.addEventListener('click', (e) => {
                    e.preventDefault();

                    let userId = e.target.dataset.userId;
                    this.selectionCallBack(parseInt(userId));
                    this.hidePopup();
                });
            }
        }

        setCallBack(selectionCallBack) {
            this.selectionCallBack = selectionCallBack;
        }

        showPopup() {
            this.userListPopup.classList.remove('user-selector-popup-hidden');
        }

        hidePopup() {
            this.userListPopup.classList.add('user-selector-popup-hidden');
        }

    }

    (new DepartmentForm(<?= json_encode(array_map(fn(User $u) => $u->id, $department->members)) ?>)).initEvents();
</script>
