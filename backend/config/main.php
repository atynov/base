<?php

use yii\web\UrlManager;
use modules\users\Bootstrap as UserBootstrap;
use modules\rbac\Bootstrap as RbacBootstrap;
use modules\news\Bootstrap as NewsBootstrap;
use modules\organization\Bootstrap as OrganizationBootstrap;
use modules\directory\Bootstrap as DirectoryBootstrap;
use modules\reports\Bootstrap as ReportsBootstrap;
use modules\rbac\Module;
use modules\users\models\User;
use modules\rbac\components\behavior\AccessBehavior;
use modules\rbac\models\Permission;
use modules\users\behavior\LastVisitBehavior;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'homeUrl' => '/admin',
    'controllerNamespace' => 'backend\controllers',
    'defaultRoute' => 'default/index',
    'bootstrap' => [
        'log',
        UserBootstrap::class,
        RbacBootstrap::class,
        NewsBootstrap::class,
        OrganizationBootstrap::class,
        DirectoryBootstrap::class,
        ReportsBootstrap::class,
    ],
    'modules' => [
        'users' => [
            'isBackend' => true
        ],
        'rbac' => [
            'isBackend' => true,
            'class' => Module::class,
            'params' => [
                'userClass' => User::class
            ]
        ],
        'news' => [
            'isBackend' => true
        ],
        'organization' => [
            'isBackend' => true
        ],
        'directory' => [
            'isBackend' => true
        ],
        'reports' => [
            'isBackend' => true
        ],
    ],
    'components' => [
        'db' => require __DIR__ . '/db.php',
        'assetManager' => [
            'basePath' => '@backend/web/assets',
            'baseUrl' => '/admin/assets',
        ],
        'request' => [
            'cookieValidationKey' => '',
            'csrfParam' => '_csrf-backend',
            'baseUrl' => '/admin'
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
            'loginUrl' => ['/users/default/login']
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'backend/error'
        ],
        'urlManager' => [
            'class' => '\yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<module:\w+-\w+|\w+>/<controller:\w+-\w+|\w+>'=>'<module>/<controller>',
                '<module:\w+-\w+|\w+>/<controller:\w+-\w+|\w+>/<action:\w+-\w+|\w+>'=>'<module>/<controller>/<action>',
                '<module:\w+-\w+|\w+>/<controller:\w+-\w+|\w+>/<action:\w+-\w+|\w+>/*'=>'<module>/<controller>/<action>',

                '<controller:\w+-\w+|\w+>'=>'<controller>',
                '<controller:\w+-\w+|\w+>/<action:\w+-\w+|\w+>'=>'<controller>/<action>',
                '<controller:\w+-\w+|\w+>/<action:\w+-\w+|\w+>/*'=>'<controller>/<action>',

            ],
        ],
        'urlManagerFrontend' => [
            'class' => UrlManager::class,
            'baseUrl' => '',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                'email-confirm' => 'users/default/email-confirm'
            ]
        ]
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    // Последний визит
    'as afterAction' => [
        'class' => LastVisitBehavior::class
    ],
    // Доступ к админке
    'as AccessBehavior' => [
        'class' => AccessBehavior::class,
        'permission' => Permission::PERMISSION_VIEW_ADMIN_PAGE, // Разрешение доступа к админке
    ],
    'params' => $params,
];
