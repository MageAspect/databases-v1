<?php

/**
 * @author Mark Prohorov <mark@intervolga.ru>
 */


namespace application\core;


use application\core\entity\Route;
use application\core\exception\ActionException;
use application\core\exception\ControllerException;
use application\core\exception\RenderException;
use application\core\exception\RouteException;
use application\module\user\facade\AuthException;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;


class Application {

    protected Router $router;
    protected View $view;
    protected static ?Application $instance;

    private function __construct(?Router $router = null, ?View $view = null) {
        if (!$router) {
            $rawRoutes = require $_SERVER['DOCUMENT_ROOT'] . '/application/config/routes.php';
            $this->router = new Router($rawRoutes);
        }
        $this->view = $view ?? new View();
    }

    public static function getInstance(): Application {
        if (!Application::$instance) {
            Application::$instance = new Application();
        }
        return Application::$instance;
    }

    /**
     * Точка входа в приложение
     * @throws RenderException
     */
    public function run(): void {
        try {
            if (!$this->router->matchUrl()) {
                throw new RouteException('Маршрут соответствующий url не найден.');
            }

            $route = $this->router->getRoute();

            $this->executeAction($route);
        } catch (AuthException $e) {
            $this->view->redirect('/');
        } catch (ControllerException | ActionException | RouteException $e) {
            $page = new Page();
            $page->responseCode = 404;
            $page->data = array(
                    'data' => array(
                            'errorText' => 'Страница не найдена'
                    )
            );
            $this->view->render($page);
        } catch (Exception $e) {
            $page = new Page();
            $page->responseCode = 500;
            $page->data = array(
                    'data' => array(
                            'errorText' => 'Что-то пошло не так :|'
                    )
            );
            $this->view->render($page);
        }
    }

    /**
     * Возвращает объект контроллера полученного из текущего маршрута.
     * @param string $controllerClass - имя класса контроллера
     * @return AdminController|PublicController|AjaxController
     * @throws ControllerException
     */
    private function getController(string $controllerClass): AdminController|PublicController|AjaxController {
        if (empty($controllerClass)) {
            throw new ControllerException('Контроллер не найден в текущем маршруте');
        }

        try {
            $controller = new ReflectionClass($controllerClass);

            if (
                    !$controller->isSubclassOf(AdminController::class)
                    && !$controller->isSubclassOf(PublicController::class)
                    && !$controller->isSubclassOf(AjaxController::class)
            ) {
                throw new ControllerException('Контроллер обязан быть наследником класса "AdminController" или "PublicController" или "AjaxController"');
            }
        } catch (ReflectionException $exception) {
            throw new ControllerException('Контроллер не найден', 0, $exception);
        }

        return new $controllerClass();
    }

    /**
     * Возвращает ReflectionMethod Action-а полученного из текущего маршрута,
     * если этот метод публичный и существует в текущем контроллере
     * @param AjaxController|PublicController|AdminController $controller
     * @param string $actionName
     * @return ReflectionMethod
     * @throws ActionException
     */
    private function getAction(AdminController|PublicController|AjaxController $controller, string $actionName): ReflectionMethod {
        if (empty($actionName)) {
            throw new ActionException('Action не найден в текущем маршруте');
        }
        try {
            $action = new ReflectionMethod($controller, $actionName);
        } catch (ReflectionException $exception) {
            echo $exception->getMessage();
            throw new ActionException('Не удалось найти Action', 0, $exception);
        }

        if (!$action->isPublic()) {
            throw new ActionException('Action должен быть объявлен, как публичный метод.');
        }

        return $action;
    }

    /**
     * @param Route $route
     * @throws ActionException
     * @throws ControllerException
     * @throws AuthException
     * @throws RenderException
     */
    private function executeAction(Route $route): void {
        $controller = $this->getController($route->controllerClass);
        $action = $this->getAction($controller, $route->actionName);

        try {
            $action->invoke($controller, $route->variables);
        } catch (ReflectionException $exception) {
            throw new ActionException('Не удалось запустить Action', 0, $exception);
        }
    }
}