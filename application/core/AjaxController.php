<?php


namespace application\core;


abstract class AjaxController {

    protected function getParams(): array {
        $params = file_get_contents('php://input');
        if (empty($params)) {
            return array();
        }

        return json_decode($params, true) ?: array();
    }

    protected function sendResult($result): void {
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}