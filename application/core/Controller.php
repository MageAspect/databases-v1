<?php


namespace application\core;


use application\core\exception\ActionException;
use application\core\exception\RenderException;
use application\module\user\facade\AuthException;
use ReflectionException;
use ReflectionMethod;


abstract class Controller {
    /**
     * @throws ActionException
     * @throws AuthException
     * @throws RenderException
     */
    public function invokeAction(string $actionName, array $actionArgs): void {
        try {
            $this->getAction($actionName)->invoke($this, $actionArgs);
        } catch (ReflectionException $e) {
            throw new ActionException("Не удалось запустить Action: $actionName", 0, $e);
        }
    }

    /**
     * Возвращает ReflectionMethod action-а,
     * если этот метод публичный и существует в текущем контроллере
     * @throws ActionException
     */
    protected function getAction(string $actionName): ReflectionMethod {
        if (empty($actionName)) {
            throw new ActionException('Action не найден в текущем маршруте');
        }
        try {
            $action = new ReflectionMethod($this, $actionName);
        } catch (ReflectionException $e) {
            throw new ActionException('Не удалось найти Action', 0, $e);
        }

        if (!$action->isPublic()) {
            throw new ActionException('Action должен быть объявлен, как публичный метод.');
        }

        return $action;
    }
}