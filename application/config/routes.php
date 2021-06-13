<?php

use application\core\entity\Route;
use application\module\user\controller\AuthController;


return array(
        new Route(
                '#^$#',
                 AuthController::class,
                 'logInAction'
        ),
        new Route(
                '#^admin/form/(?P<id>\d+)/edit$#',
                AuthController::class,
                '123'
        ),
);
