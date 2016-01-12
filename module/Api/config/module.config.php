<?php
use Api\Controller\UserController;
use Api\Controller\PostController;
use Api\Controller\CommentController;

return array(
    'controllers' => array(
        'invokables' => array(
            'apiUser' => UserController::class,
            'apiPost' => PostController::class,
            'apiComment' => CommentController::class
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
                    'user-update' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/update',

                            'defaults' => array(
                                'action' => 'update'
                            ),
                        ),
                    ),
                ),
            ),
            'apiPostRoutes' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api/post',
                    'defaults' => array(
                        'controller' => 'apiPost',
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
                                'action' => 'view'
                            ),
                        ),
                    ),
                    'post-add' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/add',
                            'defaults' => array(
                                'action' => 'add'
                            ),
                        ),
                    ),
                ),
            ),
            'apiCommentRoutes' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api/comment',
                    'defaults' => array(
                        'controller' => 'apiComment',
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
                                'action' => 'view'
                            ),
                        ),
                    ),
                    'comment-add' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/add',
                            'defaults' => array(
                                'action' => 'add'
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