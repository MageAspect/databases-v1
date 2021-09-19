<?php


namespace application\core;


use application\core\entity\Page;
use application\module\user\facade\AuthException;
use application\module\user\facade\UserFacade;
use application\module\user\facade\UserFacadeException;


abstract class AuthorizedController extends Controller {
    protected View $view;
    protected UserFacade $userFacade;

    /**
     * @throws AuthException
     */
    public function __construct(?View $view = null, ?UserFacade $userFacade = null) {
        $this->view = $view ?? new View();
        $this->userFacade = $userFacade ?? new UserFacade();

        try {
            $this->userFacade->getCurrentUser();
        } catch (UserFacadeException $e) {
            throw new AuthException();
        }
    }

    protected function getDefaultPage(): Page {
        $page = new Page();
        $page->headerFile = $_SERVER['DOCUMENT_ROOT'] . '/application/templates/header.php';
        $page->sidebarFile = $_SERVER['DOCUMENT_ROOT'] . '/application/templates/sidebar.php';
        $page->footerFile = $_SERVER['DOCUMENT_ROOT'] . '/application/templates/footer.php';
        return $page;
    }
}