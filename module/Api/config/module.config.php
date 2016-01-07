<?php
use Api\Controller\UserController;

return array(
    'controllers' => array(
        'invokables' => array(
            'apiUser' => UserController::class
        ),
    ),
    'router' => array(
        'routes' => array(
            'apiUserRoutes' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api/user',
                    'defaults' => array(
                        'controller' => 'apiUser',
                        'action' => 'index'
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(

                    'view' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/view/[:id]',
                            'constraints' => array(
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
//                                'controller' => 'apiuser',
                                'action' => 'view'
                            ),
                        ),
                    ),
                    'user-login' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/login',
                            'defaults' => array(
                                'action' => 'user-login'
                            ),
                        ),
                    ),
                    'user-register' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/register',

                            'defaults' => array(
                                'action' => 'register'
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy'
        )
    ),
);