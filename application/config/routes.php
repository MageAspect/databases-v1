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
                '#^departments/(?P<id>\d+)/details$#',
                        \application\module\department\DepartmentController::class,
                'detailsAction'
        ),
        new Route(
                '#^departments/(?P<id>\d+)/edit$#',
                \application\module\department\DepartmentController::class,
                'editAction'
        ),
        new Route(
                '#^departments/(?P<id>\d+)/delete$#',
                \application\module\department\DepartmentController::class,
                'deleteAction'
        ),
        new Route(
                '#^api/1/user/(?P<id>\d+)$#',
                \application\module\user\controller\ApiController::class,
                'getUserAction'
        ),
        new Route(
                '#^users$#',
                \application\module\user\controller\UserController::class,
                'listAction'
        ),
        new Route(
                '#^users/(?P<id>\d+)/details$#',
                \application\module\user\controller\UserController::class,
                'detailsAction'
        ),
        new Route(
                '#^users/(?P<id>\d+)/edit$#',
                \application\module\user\controller\UserController::class,
                'editAction'
        ),
        new Route(
                '#^users/(?P<id>\d+)/delete$#',
                \application\module\user\controller\UserController::class,
                'deleteAction'
        ),
);
