<?php

namespace application\module\user\controller;


use application\core\AuthorizedController;


class UserController extends AuthorizedController {
    public function listAction () {
        $page = $this->getDefaultPage();
        $page->contentFile = realpath(__DIR__ . '/../pages/list.php');

        $page->title = 'Список пользователей';


        $users = $this->userFacade->getAllUsers();
        $page->data['current-user'] = $this->userFacade->getCurrentUser();
        $page->data['users'] = $users;

        $this->view->render($page);
    }
}