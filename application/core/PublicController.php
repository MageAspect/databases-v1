<?php


namespace application\core;


use application\module\user\control\SessionUserNotFoundException;
use application\module\user\facade\UserFacade;


abstract class PublicController {
    protected View $view;
    protected UserFacade $userFacade;
    protected array $dataToView = array();

    public function __construct(?View $view = null, ?UserFacade $userFacade = null) {
        $this->view = $view ?? new View();
        $this->userFacade = $userFacade ?? new UserFacade();

        try {
            $this->dataToView['system']['user'] = $this->userFacade->getUser();
        } catch (SessionUserNotFoundException $e) {
        }
    }

    protected function getDefaultPage(): Page {
        $page = new Page();
        $page->headerFile = $_SERVER['DOCUMENT_ROOT'] . '/application/layouts/header.php';
        $page->footerFile = $_SERVER['DOCUMENT_ROOT'] . '/application/layouts/footer.php';
        return $page;
    }
}