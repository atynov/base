<?php

namespace modules\news;

use Yii;

/**
 * Class Module
 * @package modules\news
 */
class Module extends \common\components\BaseModule
{

    public $name = "News";

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
    public $controllerNamespace = 'modules\news\controllers\frontend';

    /**
     * @var bool Если модуль используется для админ-панели.
     */
    public $isBackend;


    public $imagePath = "/uploads/news";

    public $baseRoute = "/news";

    /**
     * @var string the default routes to render news categories (use "/" - for root)
     */
    public $newsCategoriesRoute = "/news/categories";

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Это здесь для того, чтобы переключаться между frontend и backend
        if ($this->isBackend === true) {
            $this->controllerNamespace = 'modules\news\controllers\backend';
            $this->setViewPath('@modules/news/views/backend');
        } else {
            $this->setViewPath('@modules/news/views/frontend');
        }

        if (isset(Yii::$app->params["news.baseRoute"]))
            $this->baseRoute = Yii::$app->params["news.baseRoute"];

        if (isset(Yii::$app->params["news.defaultController"]))
            $this->defaultController = Yii::$app->params["news.defaultController"];

        if (isset(Yii::$app->params["news.supportLocales"]))
            $this->supportLocales = Yii::$app->params["news.supportLocales"];

//        if (!isset($this->baseRoute))
//            throw new InvalidConfigException("Required module property `baseRoute` isn't set.");

        // Process and normalize route for news in frontend
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
        return Yii::t('modules/news/' . $category, $message, $params, $language);
    }

    /**
     * @return array
     */
    public static function backendMenuItems()
    {
        /** @var yii\web\User $user */
        $user = Yii::$app->user;

        return [
            'label' => '<i class="fa fa-newspaper-o" aria-hidden="true"></i> <span>' . Yii::t('app', 'Новости') . '</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>',
            'url' => ['/news/news'],
            'options' => ['class' => 'treeview'],
//                    'visible' => Yii::$app->user->can(\modules\rbac\models\Permission::PERMISSION_MANAGER_RBAC),
            'items' => [
                [
                    'label' => '<i class="fa fa-circle-o"> </i><span>' . Yii::t('app', 'Новости') . '</span>',
                    'url' => ['/news/news'],
                ],
                [
                    'label' => '<i class="fa fa-circle-o"> </i><span>' . Yii::t('app', 'Категории') . '</span>',
                    'url' => ['/news/cats'],
                ],
            ],
        ];
    }
}
