<?php

/**
 * @author Mark Prohorov <mark@intervolga.ru>
 */


namespace application\module\department;


use application\core\AuthorizedController;
use application\core\exception\RenderException;
use application\core\View;
use application\module\user\facade\UserFacade;


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

        $allDepartments = $this->departmentFacade->getAllDepartments();
        $page->data['departments'] = $allDepartments;

        $this->view->render($page);
    }
}