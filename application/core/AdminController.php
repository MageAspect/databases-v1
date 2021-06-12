<?php


namespace application\core;


use application\module\user\facade\AuthException;
use application\module\user\facade\UserFacade;
use Exception;


abstract class AdminController {
    protected View $view;
    protected UserFacade $userFacade;
    protected array $dataToView = array();

    /**
     * @throws AuthException
     */
    public function __construct(?View $view = null, ?UserFacade $userFacade = null) {
        $this->view = $view ?? new View();
        $this->userFacade = $userFacade ?? new UserFacade();

        if (!$this->userFacade->isAuthorisedUser()) {
            throw new AuthException();
        }

        try {
            $this->dataToView['system']['user'] = $this->userFacade->getUser();
        } catch (Exception $e) {
            throw new AuthException();
        }
    }

    protected function getDefaultPage(): Page {
        $page = new Page();
        $page->headerFile = $_SERVER['DOCUMENT_ROOT'] . '/application/layouts/adminHeader.php';
        $page->footerFile = $_SERVER['DOCUMENT_ROOT'] . '/application/layouts/adminFooter.php';
        return $page;
    }
}