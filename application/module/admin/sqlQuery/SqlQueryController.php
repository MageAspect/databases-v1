<?php

namespace application\module\admin\sqlQuery;


use application\core\View;
use application\module\user\facade\AuthException;
use application\module\user\facade\UserFacade;
use application\module\user\facade\UserFacadeException;


class SqlQueryController extends \application\core\AuthorizedController {
    private SqlQueryFacade $sqlQueryFacade;

    public function __construct(?View $view = null, ?UserFacade $userFacade = null) {
        parent::__construct($view, $userFacade);

        try {
            if (!$this->userFacade->getCurrentUser()->isAdmin) {
                throw new AuthException();
            }
        } catch (UserFacadeException $e) {
            throw new AuthException();
        }
        $this->sqlQueryFacade = new SqlQueryFacade();
    }


    public function sqlAction() {
        $page = $this->getDefaultPage();
        $page->contentFile = realpath(__DIR__ . '/pages/sql.php');

        $page->title = 'SQL запрос';

        $user = $this->userFacade->getCurrentUser();

        try {
            if (isset($_POST['sql'])) {
                $page->data['sql-results'] = $this->sqlQueryFacade->executeRawQuery($_POST['sql'], $user->id);
            }
        } catch (SqlQueryFacadeException | UserFacadeException $e) {
            $page->data['sql-error'] = $e->getMessage();
        }

        try {
            $page->data['sql-history'] = $this->sqlQueryFacade->getSqlHistory($user->id);
        } catch (SqlQueryFacadeException $e) {
            $page->data['error'] = $e->getMessage();
        }

        $this->view->render($page);
    }
}