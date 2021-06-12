<?php


namespace application\core;


use application\core\entity\Route;
use application\core\exception\RouteException;
use RuntimeException;


class Router {

    /**
     * @var Route[]
     */
    private array $allRoutes;

    public function __construct(array $rawRoutes) {
        foreach ($rawRoutes as $routeTemplate => $routeInfo) {
            $route = new Route();

            $route->template = $routeTemplate;
            $route->actionName = $routeTemplate;
            $route->controllerClass = $routeTemplate;

            $this->allRoutes[] = $route;
        }
    }

    /**
     * Проверка url на соответствие одному из существующих шаблонов маршрутов
     * @throws RuntimeException
     */
    public function matchUrl(): bool {
        $clearUrl = $this->getClearRequestUrl();

        foreach ($this->allRoutes as $route) {
            if (preg_match($route->template, $clearUrl)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Возвращает запрашиваемый url без GET параметоров, имени протокола и домена
     * @example https://example.org/hello/world?key=23sdf -> hello/world
     */
    private function getClearRequestUrl(): string {
        $urlWithParams = $_SERVER['REQUEST_URI'];
        return trim(explode('?', $urlWithParams)[0], '/');
    }

    /**
     * Определение из маршрута названия контроллера, action-а, переменных маршрута(которые могут задаются в шаблоне маршрута)
     * и записывает эти заданные в объект Route
     * @throws RouteException
     */
    public function getRoute(): Route {
        $clearUrl = $this->getClearRequestUrl();

        foreach ($this->allRoutes as $route) {
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

        throw new RouteException('Данные по существующему маршруту не найдены');
    }
}
