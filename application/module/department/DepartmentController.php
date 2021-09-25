<?php

/**
 * @author Mark Prohorov <mark@intervolga.ru>
 */


namespace application\module\department;


use application\core\AuthorizedController;
use application\core\exception\RenderException;
use application\core\View;
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


        $allDepartments = $this->departmentFacade->getAllDepartments();

        $page->data['user-perms'] = array();
        foreach ($allDepartments as $department) {
            $page->data['user-perms'][$department->id] = array(
                    'is-user-department-head' => $department->head->id == $this->userFacade->getCurrentUser()->id,
                    'is-user-admin' => $this->userFacade->getCurrentUser()->isAdmin
            );
        }

        $page->data['departments'] = $allDepartments;

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
            $department = $this->departmentFacade->getDepartmentById($pageParams['id']);
            $currentUser = $this->userFacade->getCurrentUser();
            $page->title = $department->name;

            $page->data['is-user-department-head'] = $department->head->id = $currentUser->id;
            $page->data['is-user-admin'] = $department->head->id = $currentUser->id;

            if (!$this->departmentFacade->canUserEditDepartment($currentUser->id, $department->id)) {
                $this->view->redirect('/');
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

        } catch (DepartmentFacadeException | UserFacadeException $e) {
            $page->data['errors'][] = $e->getMessage();
            $page->title = 'Ошибка удаления';
        }
    }
}