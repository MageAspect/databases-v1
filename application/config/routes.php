<?php


use application\core\entity\Route;


return array(
        new Route(
                '#^$#',
                 \application\module\user\controller\AuthController::class,
                 'logInAction'
        ),
        new Route(
                '#^departments$#',
                \application\module\department\DepartmentController::class,
                'listAction'
        ),
);
