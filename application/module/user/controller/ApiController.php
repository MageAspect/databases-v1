<?php

namespace application\module\user\controller;


use application\core\AuthorizedController;
use application\module\user\facade\UserFacadeException;


class ApiController extends AuthorizedController {
    public function getUserAction($apiParams) {
        try {
            $user = $this->userFacade->getUserById($apiParams['id']);
            echo json_encode($user, JSON_UNESCAPED_UNICODE);
        } catch (UserFacadeException $e) {
            echo json_encode(array('error' => $e->getMessage()), JSON_UNESCAPED_UNICODE);
        }
    }
}