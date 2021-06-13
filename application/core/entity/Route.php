<?php


namespace application\core\entity;


class Route {
    public string $template;
    public string $controllerClass = '';
    public string $actionName = '';

    /** Переменные марштура, заданные в шаблоне марштура через regex */
    public array $variables = array();

    public function __construct($template, $controllerClass, $actionName) {
        $this->template = $template;
        $this->controllerClass = $controllerClass;
        $this->actionName = $actionName;
    }
}