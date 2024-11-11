<?php

namespace modules\news;

use Yii;
use yii\i18n\PhpMessageSource;
use yii\web\GroupUrlRule;

/**
 * Class Bootstrap
 * @package modules\news
 */
class Bootstrap
{

    public $baseRoute = "/news";

    /**
     * @var string, the default controller for news in @frontend
     */
    public $defaultController = "default";

    public function __construct()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['modules/news/*'] = [
            'class' => PhpMessageSource::class,
            'basePath' => '@modules/news/messages',
            'fileMap' => [
                'modules/news/module' => 'module.php',
                'modules/news/backend' => 'backend.php',
                'modules/news/frontend' => 'frontend.php',
            ],
        ];

        $urlManager = Yii::$app->urlManager;
        $urlManager->addRules((Yii::$app->id === 'app-backend') ? [$this->rulesBackend()] : [$this->rulesFrontend()]);
    }

    protected function rulesFrontend()
    {
//        var_dump($this->defaultController);exit;
//        return [
//            'class' => 'yii\web\GroupUrlRule',
//            'rules' => [
//                '/<alias:[\w-]+>' => $this->defaultController . '/view/?alias=<alias>',
//                // $this->baseRoute => $this->defaultController . '/index',
//                //'<_a:[\w\-]+>' => 'users/default/<_a>',
//            ],
//        ];

        return [
            'class' => GroupUrlRule::class,

            'rules' => [
                'news/<alias:[\w-]+>' => 'news/default/view',
                'news' => 'news/default/index',
            ]
        ];

        /*
//        if (!$this->isBackend() && !is_null($this->defaultController)) {

            // Get language scheme if available
            $custom = false;
            $hide = false;
            $scheme = null;
            if (isset(Yii::$app->translations)) {
                $custom = true;
                $hide = Yii::$app->translations->module->hideDefaultLang;
                $scheme = Yii::$app->translations->module->languageScheme;
            }

            // Add routes for frontend
            switch ($scheme) {
                case "after":

                    $app->getUrlManager()->addRules([
                        $this->baseRoute . '/<alias:[\w-]+>/<lang:\w+>' => 'admin/news/default/view',
                        $this->baseRoute . '/<lang:\w+>' => $this->defaultController . '/index',
                    ], true);

                    if ($hide) {
                        $app->getUrlManager()->addRules([
                            $this->baseRoute . '/<alias:[\w-]+>' => 'admin/news/default/view',
                            $this->baseRoute => $this->defaultController . '/index',
                        ], true);
                    }

                    break;

                case "query":

                    $app->getUrlManager()->addRules([
                        $this->baseRoute . '/<alias:[\w-]+>' => $this->defaultController . '/view',
                        $this->baseRoute => $this->defaultController . '/index',
                    ], true);


                    break;

                case "subdomain":

                    if ($host = $app->getRequest()->getHostName()) {
                        $app->getUrlManager()->addRules([
                            'http(s)?://' . $host. '/' . $this->baseRoute . '/<alias:[\w-]+>' => $this->defaultController . '/view',
                            'http(s)?://' . $host. '/' . $this->baseRoute => $this->defaultController . '/index',
                        ], true);

                    }

                    break;

                default:

                    $app->getUrlManager()->addRules([
                        '/<lang:\w+>' . $this->baseRoute . '/<alias:[\w-]+>' => $this->defaultController . '/view',
                        '/<lang:\w+>' . $this->baseRoute => $this->defaultController . '/index',
                    ], true);

                    if ($hide || !$custom) {
                        $app->getUrlManager()->addRules([
                            $this->baseRoute . '/<alias:[\w-]+>' => $this->defaultController . '/view',
                            $this->baseRoute => $this->defaultController . '/index',
                        ], true);
                    }

                    break;
            }
        */
    }


    protected function rulesBackend()
    {
        return [
            'class' => 'yii\web\GroupUrlRule',
            'rules' => [
                'users' => 'users/default/index',
                'user/<id:\d+>/<_a:[\w\-]+>' => 'users/default/<_a>',
                'user/<_a:[\w\-]+>' => 'users/default/<_a>',

                'profile' => 'users/profile/index',
                'profile/<id:\d+>/<_a:[\w\-]+>' => 'users/profile/<_a>',
                'profile/<_a:[\w\-]+>' => 'users/profile/<_a>',
            ],
        ];
    }

}
