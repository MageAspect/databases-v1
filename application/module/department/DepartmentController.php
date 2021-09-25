<?php

/**
 * @author Mark Prohorov <mark@intervolga.ru>
 */


namespace application\module\department;


use application\core\AuthorizedController;
use application\core\exception\RenderException;
use application\core\View;
use application\module\user\entity\User;
use application\module\user\facade\UserFacade;
use application\module\user\facade\UserFacadeException;


class DepartmentController extends AuthorizedController {
    private DepartmentFacade $departmentFacade;

    public function __construct(?View $view = null, ?UserFacade $userFacade = null) {
        $this->departmentFacade = new DepartmentFacade();
        parent::__construct($view, $userFacade);
    }

    /**
     * @throws RenderException
     */
    public function listAction(): void {
        $page = $this->getDefaultPage();
        $page->contentFile = __DIR__ . '/pages/list.php';
        $page->title = 'Список подразделений';

        try {
            $allDepartments = $this->departmentFacade->getAllDepartments();

            $page->data['user-perms'] = array();
            foreach ($allDepartments as $department) {
                $page->data['user-perms'][$department->id] = array(
                        'is-user-department-head' => $department->head->id == $this->userFacade->getCurrentUser()->id,
                        'is-user-admin' => $this->userFacade->getCurrentUser()->isAdmin
                );
            }
            $page->data['departments'] = $allDepartments;
            $page->data['current-user'] = $this->userFacade->getCurrentUser();

        } catch (UserFacadeException | DepartmentFacadeException $e) {
            $page->data['errors'][] = $e->getMessage();
        }

        $this->view->render($page);
    }

    /**
     * @throws RenderException
     */
    public function detailsAction(array $pageParams) {
        $page = $this->getDefaultPage();
        $page->contentFile = __DIR__ . '/pages/details.php';

        try {
            $department = $this->departmentFacade->getDepartmentById($pageParams['id']);
            $page->title = $department->name;

            $page->data['department'] = $department;
        } catch (DepartmentFacadeException $e) {
            $page->data['errors'][] = $e->getMessage();
            $page->title = 'Подразделение детально';
        }

        $this->view->render($page);
    }

    /**
     * @throws RenderException
     */
    public function editAction(array $pageParams) {
        $page = $this->getDefaultPage();
        $page->contentFile = __DIR__ . '/pages/edit.php';

        try {
            $currentUser = $this->userFacade->getCurrentUser();

            if ($pageParams['id'] > 0) {
                $department = $this->departmentFacade->getDepartmentById($pageParams['id']);

            } else {
                $department = new Department();
                $department->id = 0;
                $department->head = $currentUser;
                $department->description = '';
                $department->name = 'Новое подразделение';
            }
            $page->title = $department->name;

            $page->data['is-user-admin'] = $currentUser->isAdmin;
            $page->data['available-members'] = $this->userFacade->getAllUsers();

            if ($pageParams['id'] > 0 && !$this->departmentFacade->canUserEditDepartment($currentUser->id,
                            $department->id)) {
                $this->view->redirect('/');
            } elseif ($pageParams['id'] == 0 && !$currentUser->isAdmin) {
                $this->view->redirect('/');
            }

            if (isset($_POST['title'])) {
                $department->name = $_POST['title'];
                $department->head->id = $_POST['head-id'];
                $department->description = $_POST['description'];

                $courseMembersIds = array_map(fn(User $u) => $u->id, $department->members);

                $newMembersIds = !empty($_POST['members-ids']) ? explode(',', $_POST['members-ids']) : array();
                $newMembersIds = array_map(fn(string $id) => (int)$id, $newMembersIds);

                $membersToDelete = array_diff($courseMembersIds, $newMembersIds);
                $membersToAdd = array_diff($newMembersIds, $courseMembersIds);

                if ($pageParams['id'] > 0) {
                    $this->departmentFacade->updateDepartment($department);
                } else {
                    $department->id = $this->departmentFacade->addDepartment($department);
                }

                foreach ($membersToAdd as $memberId) {
                    $this->departmentFacade->addMemberToDepartment($memberId, $department->id);
                }
                foreach ($membersToDelete as $memberId) {
                    $this->departmentFacade->removeMemberFromDepartment($memberId, $department->id);
                }

                $this->view->redirect("/departments/$department->id/details");
            }

            $page->data['department'] = $department;
        } catch (DepartmentFacadeException | UserFacadeException $e) {
            $page->data['errors'][] = $e->getMessage();
            $page->title = 'Редактирование подразделения';
        }

        $this->view->render($page);
    }


    public function deleteAction(array $pageParams) {
        $page = $this->getDefaultPage();
        $page->contentFile = __DIR__ . '/pages/edit.php';

        try {
            $currentUser = $this->userFacade->getCurrentUser();

            if (!$currentUser->isAdmin) {
                $this->view->redirect('/');
            } else {
                $this->departmentFacade->deleteDepartment($pageParams['id']);
            }

            $this->view->redirect('/departments/');
        } catch (DepartmentFacadeException | UserFacadeException $e) {
            $page->data['errors'][] = $e->getMessage();
            $page->title = 'Ошибка удаления';
        }
    }
}