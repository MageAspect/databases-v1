<?php

namespace application\module\user\controller;


use application\core\AuthorizedController;
use application\core\View;
use application\module\department\DepartmentFacade;
use application\module\department\DepartmentFacadeException;
use application\module\user\facade\AuthException;
use application\module\user\facade\UserCarrierJournalFacade;
use application\module\user\facade\UserCarrierJournalFacadeException;
use application\module\user\facade\UserFacade;
use application\module\user\facade\UserFacadeException;


class UserController extends AuthorizedController {
    private DepartmentFacade $departmentFacade;
    private UserCarrierJournalFacade $userCarrierJournalFacade;

    public function __construct(?View $view = null, ?UserFacade $userFacade = null) {
        parent::__construct($view, $userFacade);
        $this->departmentFacade = new DepartmentFacade();
        $this->userCarrierJournalFacade = new UserCarrierJournalFacade();
    }

    public function listAction () {
        $page = $this->getDefaultPage();
        $page->contentFile = realpath(__DIR__ . '/../pages/list.php');
        $page->title = 'Список пользователей';

        try {
            $users = $this->userFacade->getAllUsers();
            $page->data['current-user'] = $this->userFacade->getCurrentUser();
            $page->data['users'] = $users;

        } catch (UserFacadeException $e) {
            $page->data['errors'][] = $e->getMessage();
        }

        $this->view->render($page);
    }

    public function detailsAction(array $pageParams) {
        $page = $this->getDefaultPage();
        $page->contentFile = realpath(__DIR__ . '/../pages/details.php');

        $userId = $pageParams['id'];
        try {
            $user = $this->userFacade->getUserById($userId);
            $page->data['user-departments'] = $this->departmentFacade->getUserDepartments($userId);
            $page->data['user'] = $user;
            $page->data['user-carrier-journal'] = $this->userCarrierJournalFacade->getUserCarrierJournal($userId);
            $page->data['can-read-security-fields'] = $this->userFacade->getCurrentUser()->id == $userId
                    ||  $this->userFacade->getCurrentUser()->isAdmin;

            $page->title = "Профиль сотрудника: $user->lastName $user->name";

        } catch (UserFacadeException | DepartmentFacadeException | UserCarrierJournalFacadeException $e) {
            $page->title = "Профиль сотрудника";
            $page->data['errors'][] = $e->getMessage();
        }

        $this->view->render($page);
    }

    /**
     * @throws AuthException
     * @throws \application\core\exception\RenderException
     */
    public function editAction(array $pageParams) {
        $page = $this->getDefaultPage();
        $page->contentFile = realpath(__DIR__ . '/../pages/edit.php');

        $userId = $pageParams['id'];
        try {
            $user = $this->userFacade->getUserById($userId);
            $currentUser = $this->userFacade->getCurrentUser();

            if ($user->id != $currentUser->id && !$currentUser->isAdmin) {
                throw new AuthException();
            }

            $page->data['user-departments'] = $this->departmentFacade->getUserDepartments($userId);
            $page->data['user'] = $user;
            $page->data['user-carrier-journal'] = $this->userCarrierJournalFacade->getUserCarrierJournal($userId);
            $page->data['can-edit-work-fields'] = $currentUser->isAdmin;
            $page->data['can-read-security-fields'] = $currentUser->id == $userId
                    ||  $currentUser->isAdmin;

            $page->data['can-change-password'] = $currentUser->id == $userId
                    ||  $currentUser->isAdmin;
            $page->title = "Редактирование профиля: $user->lastName $user->name";

            if (isset($_POST['submitted'])) {
                $user->name = $_POST['user-name'];
                $user->lastName = $_POST['user-last-name'];
                if ($currentUser->isAdmin) {
                    $user->position = $_POST['user-position'];
                    $user->salary = $_POST['user-salary'];
                }
                $user->patronymic = $_POST['user-patronymic'];
                $user->email = $_POST['user-email'];
                $user->phone = $_POST['user-phone'];
                if ($_POST['delete-user-avatar'] == 1) {
                    $user->pathToAvatar = '';
                }

                if ($currentUser->id == $userId || $currentUser->isAdmin) {
                    $this->userFacade->updateUser($user, $_POST['user-password']);
                    $this->view->redirect("/users/$user->id/details");
                }
            }

        } catch (/*UserFacadeException |*/ DepartmentFacadeException | UserCarrierJournalFacadeException $e) {
            $page->title = "Редактирование профиля сотрудника";
            $page->data['errors'][] = $e->getMessage();
        }

        $this->view->render($page);
    }
}