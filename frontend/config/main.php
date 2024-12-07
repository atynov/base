<?php

use yii\web\UrlManager;
use yii\log\FileTarget;
use modules\users\models\User;
use modules\users\behavior\LastVisitBehavior;
use modules\main\Bootstrap as MainBootstrap;
use modules\users\Bootstrap as UserBootstrap;
use modules\rbac\Bootstrap as RbacBootstrap;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'homeUrl' => '/',
    'language' => 'kk', // en, ru
    'sourceLanguage' => 'kk',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        MainBootstrap::class,
        UserBootstrap::class,
        RbacBootstrap::class,
    ],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'main/default/index',
    'components' => [
        'db' => require __DIR__ . '/db.php',
        'request' => [
            'cookieValidationKey' => '_Mg9l3IesMzkkxVvG1c6_C-eRyzrpe_f',
            'csrfParam' => '_csrf-frontend',
            'baseUrl' => ''
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'loginUrl' => ['/users/default/login']
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend'
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning']
                ]
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'frontend/error'
        ],
        'urlManager' => [
            'baseUrl' => '/',
            'class' => UrlManager::class,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
//                'pages' => '/pages/default/view',
//                '<module:\w+-\w+|\w+>/<controller:\w+-\w+|\w+>'=>'<module>/<controller>',
//                '<module:\w+-\w+|\w+>/<controller:\w+-\w+|\w+>/<action:\w+-\w+|\w+>'=>'<module>/<controller>/<action>',
//                '<module:\w+-\w+|\w+>/<controller:\w+-\w+|\w+>/<action:\w+-\w+|\w+>/*'=>'<module>/<controller>/<action>',
//
//                '<controller:\w+-\w+|\w+>'=>'<controller>',
//                '<controller:\w+-\w+|\w+>/<action:\w+-\w+|\w+>'=>'<controller>/<action>',
//                '<controller:\w+-\w+|\w+>/<action:\w+-\w+|\w+>/*'=>'<controller>/<action>',



            ],
        ],
        'urlManagerBackend' => [
            'class' => UrlManager::class,
            'baseUrl' => '/admin',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => []
        ],
//        'authClientCollection' => [
//            'class' => 'yii\authclient\Collection',
//            'clients' => [
//                'facebook' => [
//                    'class' => 'yii\authclient\clients\Facebook',
//                    'clientId' => '1065895384341563',
//                    'clientSecret' => '7b934b896ad9ca66c0e0c3355ce89e14',
//                ],
//            ],
//        ],
    ],
    'as afterAction' => [
        'class' => LastVisitBehavior::class
    ],
    'params' => $params
];
