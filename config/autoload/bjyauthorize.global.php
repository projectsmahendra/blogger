<?php

// For PHP <= 5.4, you should replace any ::class references with strings
// remove the first \ and the ::class part and encase in single quotes

return [
    'bjyauthorize' => [

        // set the 'guest' role as default (must be defined in a role provider)
        'default_role' => 'guest',

        /* this module uses a meta-role that inherits from any roles that should
         * be applied to the active user. the identity provider tells us which
         * roles the "identity role" should inherit from.
         * for ZfcUser, this will be your default identity provider
        */
        'identity_provider' => \BjyAuthorize\Provider\Identity\ZfcUserZendDb::class,

        /* If you only have a default role and an authenticated role, you can
         * use the 'AuthenticationIdentityProvider' to allow/restrict access
         * with the guards based on the state 'logged in' and 'not logged in'.
         *
         * 'default_role'       => 'guest',         // not authenticated
         * 'authenticated_role' => 'user',          // authenticated
         * 'identity_provider'  => \BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::class,
         */

        /* role providers simply provide a list of roles that should be inserted
         * into the Zend\Acl instance. the module comes with two providers, one
         * to specify roles in a config file and one to load roles using a
         * Zend\Db adapter.
         */
        'role_providers' => [

            // this will load roles from the user_role table in a database
            // format: user_role(role_id(varchar], parent(varchar))
            \BjyAuthorize\Provider\Role\ZendDb::class => [
                'table' => 'user_role',
                'identifier_field_name' => 'roleId',
                'role_id_field' => 'roleId',
                'parent_role_field' => 'parent_id',
            ],
        ],

        // resource providers provide a list of resources that will be tracked
        // in the ACL. like roles, they can be hierarchical
        'resource_providers' => [
            \BjyAuthorize\Provider\Resource\Config::class => [
                'pants' => [],
            ],
        ],

        /* rules can be specified here with the format:
         * [roles (array], resource, [privilege (array|string], assertion])
         * assertions will be loaded using the service manager and must implement
         * Zend\Acl\Assertion\AssertionInterface.
         * *if you use assertions, define them using the service manager!*
         */
        'rule_providers' => [
            \BjyAuthorize\Provider\Rule\Config::class => [
                'allow' => [
                    // allow guests and users (and admins, through inheritance)
                    // the "wear" privilege on the resource "pants"
                    [['guest', 'user'], 'pants', 'wear'],
                ],

                // Don't mix allow/deny rules if you are using role inheritance.
                // There are some weird bugs.
                'deny' => [
                    // ...
                ],
            ],
        ],

        /* Currently, only controller and route guards exist
         *
         * Consider enabling either the controller or the route guard depending on your needs.
         */
        'guards' => [
            /* If this guard is specified here (i.e. it is enabled], it will block
             * access to all controllers and actions unless they are specified here.
             * You may omit the 'action' index to allow access to the entire controller
             */
            \BjyAuthorize\Guard\Controller::class => [
                [
                    'controller' => 'Application\Controller\Index',
                    'action' => 'index',
                    'roles' => ['guest', 'user']
                ],
                [
                    'controller' => 'zfcuser',
                    'roles' => ['user']
                ],
                [
                    'controller' => 'zfcuser',
                    'action' => 'index',
                    'roles' => ['guest', 'user']
                ],
                [
                    'controller' => 'zfcuser',
                    'action' => 'login',
                    'roles' => ['guest']
                ],
                [
                    'controller' => 'zfcuser',
                    'action' => 'register',
                    'roles' => ['guest']
                ],
                [
                    'controller' => 'zfcuser',
                    'action' => 'logout',
                    'roles' => ['user']
                ],
                [
                    'controller' => 'apiUser',
                    'roles' => ['guest']
                ],
                [
                    'controller' => 'apiPost',
                    'roles' => ['guest']
                ],
                [
                    'controller' => 'apiComment',
                    'roles' => ['guest']
                ],
            ],
        ],
    ],
];