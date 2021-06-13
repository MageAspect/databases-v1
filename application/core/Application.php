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


class Application {

    protected Router $router;
    protected View $view;
    protected static ?Application $instance = null;
    private ErrorPageFactory $errorPageFactory;

    protected function __construct(?Router $router = null, ?View $view = null, ?ErrorPageFactory $errorPageFactory = null) {
        if (!$router) {
            $routes = require $_SERVER['DOCUMENT_ROOT'] . '/application/config/routes.php';
            $this->router = new Router($routes);
        }
        $this->view = $view ?? new View();
        $this->errorPageFactory = $errorPageFactory ?? new ErrorPageFactory();
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
            $route = $this->router->getRoute();
            $this->executeAction($route);
        } catch (AuthException $e) {
            $this->view->redirect('/');
        } catch (ControllerException | ActionException | RouteException $e) {
            $this->view->render($this->errorPageFactory->createPage404());
        } catch (Exception $e) {
            $this->view->render($this->errorPageFactory->createPage500());
        }
    }

    /**
     * Возвращает объект контроллера полученного из текущего маршрута.
     * @param string $controllerClass - имя класса контроллера вместе с пространством имён
     * @return Controller
     * @throws ControllerException
     */
    protected function getController(string $controllerClass): Controller {
        if (empty($controllerClass)) {
            throw new ControllerException('Контроллер не указан в текущем маршруте');
        }

        try {
            $controller = new ReflectionClass($controllerClass);
        } catch (ReflectionException $exception) {
            throw new ControllerException('Контроллер не найден', 0, $exception);
        }

        if (!$controller->isSubclassOf(Controller::class)) {
            throw new ControllerException('Контроллер обязан быть наследником класса \application\core\Controller"');
        }

        return new $controllerClass();
    }

    /**
     * @param Route $route
     * @throws ActionException
     * @throws ControllerException
     * @throws AuthException
     * @throws RenderException
     */
    protected function executeAction(Route $route): void {
        $controller = $this->getController($route->controllerClass);
        $controller->invokeAction($route->actionName, $route->variables);
    }
}