<?php

/**
 * @author Mark Prohorov <mark@intervolga.ru>
 */


namespace application\module\department;


use application\core\AuthorizedController;
use application\core\exception\RenderException;


class DepartmentController extends AuthorizedController {
    /**
     * @throws RenderException
     */
    public function listAction(): void {
        $page = $this->getDefaultPage();
        $page->contentFile = __DIR__ . '/pages/list.php';



        $this->view->render($page);
    }
}