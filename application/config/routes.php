<?php

return array(
        '#^?#' => array(
                'controller' => \application\controller\AuthController::class,
                'action' => 'logInAction'
        ),
        'form' => array(
                'controller' => \application\controller\FeedBackController::class,
                'action' => 'addFeedBackAction'
        ),
        'admin' => array(
                'controller' => \application\controller\AdminMainController::class,
                'action' => 'indexAction'
        ),
        'admin/form' => array(
                'controller' => \application\controller\AdminFeedBackController::class,
                'action' => 'allFeedBacksAction'
        ),
        'admin/form/(?P<id>\d+)/edit' => array(
                'controller' => \application\controller\AdminFeedBackController::class,
                'action' => 'editFeedBackAction'
        ),
        'ajax/admin/feedbacks.get' => array(
                'controller' => \application\controller\FeedBackAjaxController::class,
                'action' => 'getAllFeedBacksAction'
        ),
        'ajax/admin/feedback.delete' => array(
                'controller' => \application\controller\FeedBackAjaxController::class,
                'action' => 'deleteFeedBackAction'
        )
);
