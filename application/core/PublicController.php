<?php


namespace application\core;


use application\core\entity\Page;
use application\module\user\facade\UserFacade;
use application\module\user\facade\UserFacadeException;


abstract class PublicController extends Controller {
    protected View $view;
    protected UserFacade $userFacade;
    protected array $dataToView = array();

    public function __construct(?View $view = null, ?UserFacade $userFacade = null) {
        $this->view = $view ?? new View();
        $this->userFacade = $userFacade ?? new UserFacade();

        try {
            $this->dataToView['system']['user'] = $this->userFacade->getSessionUser();
        } catch (UserFacadeException $e) {
        }
    }

    protected function getDefaultPage(): Page {
        $page = new Page();
        $page->headerFile = $_SERVER['DOCUMENT_ROOT'] . '/application/layouts/header.php';
        $page->footerFile = $_SERVER['DOCUMENT_ROOT'] . '/application/layouts/footer.php';
        return $page;
    }
}