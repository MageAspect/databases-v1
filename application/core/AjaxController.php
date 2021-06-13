<?php


namespace application\core;


abstract class AjaxController extends Controller {

    protected function getRequestParams(): array {
        $params = file_get_contents('php://input');
        if (empty($params)) {
            return array();
        }

        return json_decode($params, true) ?: array();
    }

    protected function send($result): void {
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}