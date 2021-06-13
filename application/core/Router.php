<?php


namespace application\core;


use application\core\entity\Route;
use application\core\exception\RouteException;
use RuntimeException;


class Router {

    /**
     * @var Route[]
     */
    private array $routes;

    public function __construct(array $routes) {
        $this->routes = $routes;
    }

    /**
     * Возвращает запрашиваемый url без GET параметоров, имени протокола и домена
     * @example https://example.org/hello/world?key=23sdf -> hello/world
     */
    protected function getClearRequestUrl(): string {
        $urlWithParams = $_SERVER['REQUEST_URI'];
        return trim(explode('?', $urlWithParams)[0], '/');
    }

    /**
     * Определение из маршрута названия контроллера, action-а, переменных маршрута(которые могут задаются в шаблоне маршрута)
     * @throws RouteException
     */
    public function getRoute(): Route {
        $clearUrl = $this->getClearRequestUrl();

        foreach ($this->routes as $route) {
            if (!preg_match($route->template, $clearUrl, $matches)) {
                continue;
            }

            foreach ($matches as $variableName => $value) {
                if (!is_string($variableName) || empty($value)) {
                    continue;
                }
                $route->variables[$variableName] = $value;
            }

            return clone $route;
        }

        throw new RouteException('Не удалось найти маршрут');
    }
}
