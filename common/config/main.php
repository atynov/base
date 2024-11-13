<?php

use yii\rbac\DbManager;
use modules\main\Module as MainModule;
use modules\users\Module as UserModule;
use modules\rbac\Module as RbacModule;
use modules\news\Module as NewsModule;
use modules\organization\Module as OrganizationModule;
use modules\directory\Module as DirectoryModule;


return [
    'name' => 'Advanced',
    'timeZone' => 'Asia/Almaty',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'db' => require __DIR__ . '/db.php',
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'kk',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'authManager' => [
            'class' => DbManager::class
        ],
        'mailer' => [
            'useFileTransport' => false
        ],
        'assetManager' => [
//            'linkAssets' => true,
            'appendTimestamp' => true,
            'basePath' => '@app/web/assets'
        ]
    ],
    'modules' => [
        'main' => [
            'class' => MainModule::class
        ],
        'users' => [
            'class' => UserModule::class
        ],
        'rbac' => [
            'class' => RbacModule::class
        ],
        'news' => [
            'class' => NewsModule::class
        ],
        'organization' => [
            'class' => OrganizationModule::class
        ],
        'directory' => [
            'class' => DirectoryModule::class
        ],
    ]
];
