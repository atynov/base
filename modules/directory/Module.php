<?php

namespace modules\directory;

use Yii;

/**
 * Class Module
 * @package modules\directory
 */
class Module extends \common\components\BaseModule
{

    public $name = "Directory";

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
    public $controllerNamespace = 'modules\directory\controllers\frontend';

    /**
     * @var bool Если модуль используется для админ-панели.
     */
    public $isBackend;


    public $imagePath = "/uploads/directory";

    public $baseRoute = "/directory";

    /**
     * @var string the default routes to render directory categories (use "/" - for root)
     */
    public $directoryCategoriesRoute = "/directory/categories";

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Это здесь для того, чтобы переключаться между frontend и backend
        if ($this->isBackend === true) {
            $this->controllerNamespace = 'modules\directory\controllers\backend';
            $this->setViewPath('@modules/directory/views/backend');
        } else {
            $this->setViewPath('@modules/directory/views/frontend');
        }

        if (isset(Yii::$app->params["directory.baseRoute"]))
            $this->baseRoute = Yii::$app->params["directory.baseRoute"];

        if (isset(Yii::$app->params["directory.defaultController"]))
            $this->defaultController = Yii::$app->params["directory.defaultController"];

        if (isset(Yii::$app->params["directory.supportLocales"]))
            $this->supportLocales = Yii::$app->params["directory.supportLocales"];

//        if (!isset($this->baseRoute))
//            throw new InvalidConfigException("Required module property `baseRoute` isn't set.");

        // Process and normalize route for directory in frontend
//        $this->baseRoute = self::normalizeRoute($this->baseRoute);

        // Normalize path to image folder
        $this->imagePath = \yii\helpers\FileHelper::normalizePath($this->imagePath);
    }

    /**
     * @param string $category
     * @param string $message
     * @param array $params
     * @param null|string $language
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/directory/' . $category, $message, $params, $language);
    }

    /**
     * @return array
     */
    public static function backendMenuItems()
    {
        /** @var yii\web\User $user */
        $user = Yii::$app->user;

        return [
            'label' => '<i class="fa fa-digg" aria-hidden="true"></i> <span>' . Yii::t('app', 'Аумақтар') . '</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>',
            'url' => ['/directory/default'],
            'options' => ['class' => 'treeview'],
//                    'visible' => Yii::$app->user->can(\modules\rbac\models\Permission::PERMISSION_MANAGER_RBAC),
            'items' => [
            ],
        ];
    }
}
