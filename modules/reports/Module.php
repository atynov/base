<?php

namespace modules\reports;

use Yii;

/**
 * Class Module
 * @package modules\reports
 */
class Module extends \common\components\BaseModule
{

    public $name = "reports";

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
    public $controllerNamespace = 'modules\reports\controllers\frontend';

    /**
     * @var bool Если модуль используется для админ-панели.
     */
    public $isBackend;


    public $imagePath = "/uploads/reports";

    public $baseRoute = "/reports";

    /**
     * @var string the default routes to render reports categories (use "/" - for root)
     */
    public $reportsCategoriesRoute = "/reports/categories";

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Это здесь для того, чтобы переключаться между frontend и backend
        if ($this->isBackend === true) {
            $this->controllerNamespace = 'modules\reports\controllers\backend';
            $this->setViewPath('@modules/reports/views/backend');
        } else {
            $this->setViewPath('@modules/reports/views/frontend');
        }

        if (isset(Yii::$app->params["reports.baseRoute"]))
            $this->baseRoute = Yii::$app->params["reports.baseRoute"];

        if (isset(Yii::$app->params["reports.defaultController"]))
            $this->defaultController = Yii::$app->params["reports.defaultController"];

        if (isset(Yii::$app->params["reports.supportLocales"]))
            $this->supportLocales = Yii::$app->params["reports.supportLocales"];

//        if (!isset($this->baseRoute))
//            throw new InvalidConfigException("Required module property `baseRoute` isn't set.");

        // Process and normalize route for reports in frontend
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
        return Yii::t('modules/reports/' . $category, $message, $params, $language);
    }

    /**
     * @return array
     */
    public static function backendMenuItems()
    {
        /** @var yii\web\User $user */
        $user = Yii::$app->user;

        return [
            'label' => '<i class="fa fa-digg" aria-hidden="true"></i> <span>' . Yii::t('app', 'Есептер') . '</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>',
            'url' => ['/reports/default'],
            'options' => ['class' => 'treeview'],
//                    'visible' => Yii::$app->user->can(\modules\rbac\models\Permission::PERMISSION_MANAGER_RBAC),
            'items' => [
            ],
        ];
    }
}
