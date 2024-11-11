<?php

namespace modules\organization;

use Yii;
use yii\i18n\PhpMessageSource;
use yii\web\GroupUrlRule;

/**
 * Class Bootstrap
 * @package modules\organization
 */
class Bootstrap
{

    public function __construct()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['modules/organization/*'] = [
            'class' => PhpMessageSource::class,
            'basePath' => '@modules/organization/messages',
            'fileMap' => [
                'modules/organization/module' => 'module.php',
                'modules/organization/backend' => 'backend.php',
                'modules/organization/frontend' => 'frontend.php',
            ],
        ];

        $urlManager = Yii::$app->urlManager;
        $urlManager->addRules([
            [
                'class' => GroupUrlRule::class,

                'rules' => [
                    'cats/<alias:[\w-]+>/<subalias:[\w-]+>' => 'organization/default/list',
                    'cats/<alias:[\w-]+>' => 'organization/cats/list',
                    'cats' => 'organization/cats/index',
                ]
            ]
        ]);
    }

}
