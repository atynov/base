<?php

namespace modules\rbac;

use modules\rbac\models\Permission;
use Yii;
use yii\console\Application as ConsoleApplication;
use modules\users\models\User;

/**
 * Class Module
 * @package modules\rbac
 */
class Module extends \yii\base\Module
{
    /**
     * @var string
     */
    public $userClass = User::class;

    /**
     * @var string
     */
    public $controllerNamespace = 'modules\rbac\controllers';

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
        if (Yii::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'modules\rbac\console';
        }
        $this->setViewPath('@modules/rbac/views');
    }

    /**
     * @param string $category
     * @param string $message
     * @param array $params
     * @param string|null $language
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/rbac/' . $category, $message, $params, $language);
    }

    /**
     * @return array
     */
    public static function backendMenuItems()
    {
        /** @var yii\web\User $user */
        $user = Yii::$app->user;

        return [
            'label' => '<i class="fa fa-unlock"></i> <span>' . Yii::t('app', 'RBAC') . '</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>',
            'url' => ['/rbac/default/index'],
            'options' => ['class' => 'treeview'],
            'visible' => $user->can(Permission::PERMISSION_MANAGER_RBAC),
            'items' => [
                [
                    'label' => '<i class="fa fa-circle-o"> </i><span>' . Yii::t('app', 'Permissions') . '</span>',
                    'url' => ['/rbac/permissions/index']
                ],
                [
                    'label' => '<i class="fa fa-circle-o"> </i><span>' . Yii::t('app', 'Roles') . '</span>',
                    'url' => ['/rbac/roles/index']
                ],
                [
                    'label' => '<i class="fa fa-circle-o"> </i><span>' . Yii::t('app', 'Assign') . '</span>',
                    'url' => ['/rbac/assign/index']
                ]
            ]
        ];
    }
}
