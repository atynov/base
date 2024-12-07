<?php

use modules\rbac\Module as RbacModule;
use yii\console\controllers\MigrateController;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => MigrateController::class,
            'migrationNamespaces' => [
//                'modules\rbac\migrations',
                'modules\users\migrations',

                // TODO create dynamic load
                'modules\news\migrations',
                'modules\organization\migrations',
                'modules\directory\migrations',
                'modules\reports\migrations',
            ]
        ],
        'fixture' => [
            'class' => \yii\console\controllers\FixtureController::class,
            'namespace' => 'common\fixtures',
        ],
    ],
    'components' => [
        'db' => require __DIR__ . '/db.php',
        'log' => [
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => $params,
    'modules' => [
        'rbac' => [
            'class' => RbacModule::class,
            'params' => [
                'userClass' => \common\models\User::class
            ]
        ]
    ],
];
