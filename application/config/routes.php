<?php


use application\core\entity\Route;


return array(
        new Route(
                '#^$#',
                \application\module\user\controller\AuthController::class,
                'logInAction'
        ),
        new Route(
                '#^logout$#',
                \application\module\user\controller\AuthController::class,
                'logOutAction'
        ),
        new Route(
                '#^departments$#',
                \application\module\department\DepartmentController::class,
                'listAction'
        ),
        new Route(
                '#^departments/(?P<id>\d+)/details#',
                        \application\module\department\DepartmentController::class,
                'detailsAction'
        ),
        new Route(
                '#^departments/(?P<id>\d+)/edit#',
                \application\module\department\DepartmentController::class,
                'detailsAction'
        ),
);
