<?php

namespace modules\organization;

use common\components\BaseModule;
use Yii;

/**
 * Class Module
 * @package modules\organization
 */
class Module extends BaseModule
{
    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors()
    {
        return [
            'rateLimiter' => [
                'class' => \yii\filters\RateLimiter::class,
            ],
        ];
    }

    /**
     * @var string
     */
    public $controllerNamespace = 'modules\organization\controllers\frontend';

    /**
     * @var bool Если модуль используется для админ-панели.
     */
    public $isBackend;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Это здесь для того, чтобы переключаться между frontend и backend
        if ($this->isBackend === true) {
            $this->controllerNamespace = 'modules\organization\controllers\backend';
            $this->setViewPath('@modules/organization/views/backend');
        } else {
            $this->setViewPath('@modules/organization/views/frontend');
        }
    }


    /**
     * @return array
     */
    public static function backendMenuItems()
    {
        /** @var yii\web\User $user */
        $user = Yii::$app->user;

        return [
            'label' => '<i class="fa fa-building" aria-hidden="true"></i> <span>' . Yii::t('app', 'Организации') . '</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>',
            'url' => ['/organization/default'],
            'options' => ['class' => 'treeview'],
            'visible' => Yii::$app->user->can(\modules\rbac\models\Permission::PERMISSION_MANAGER_RBAC),
            'items' => [
                [
                    'label' => '<i class="fa fa-circle-o"> </i><span>' . Yii::t('app', 'Организации') . '</span>',
                    'url' => ['/organization/default'],
                ],
                [
                    'label' => '<i class="fa fa-circle-o"> </i><span>' . Yii::t('app', 'Категории') . '</span>',
                    'url' => ['/organization/cats'],
                ],
            ],
        ];
    }

//    /**
//     * @param string $category
//     * @param string $message
//     * @param array $params
//     * @param null|string $language
//     * @return string
//     */
//    public static function t($category, $message, $params = [], $language = null)
//    {
//        return Yii::t('modules/organization/' . $category, $message, $params, $language);
//    }
}
