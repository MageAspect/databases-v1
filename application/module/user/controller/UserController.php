<?php

namespace application\module\user\controller;


use application\core\AuthorizedController;
use application\module\user\facade\UserFacadeException;


class UserController extends AuthorizedController {
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

    public function detailsUser(array $pageParams) {

    }
}