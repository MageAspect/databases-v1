<?php


namespace application\core\entity;


class Route {
    public string $template;
    public string $controllerClass = '';
    public string $actionName = '';

    /** @var array переменные марштура, заданные в шаблоне марштура через regex */
    public array $variables = array();
}