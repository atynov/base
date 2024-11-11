<?php

namespace modules\news\controllers\backend;

use Yii;
use backend\controllers\BackendController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use modules\news\models\News;
use yii\data\ActiveDataProvider;

/**
 * DefaultController implements actions for News model.
 */
class DefaultController extends BackendController
{

    /**
     * View of news item.
     *
     * @param string $news aliases of searching news.
     * @return mixed
     * @see News::$alias
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => News::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * View of news item.
     *
     * @param $alias
     * @param null $lang
     * @param bool $draft
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionView($alias, $lang = null, $draft = false)
    {

        // Check probably need redirect to new URL
        if (isset(Yii::$app->redirects)) {
            if (Yii::$app->redirects->check(Yii::$app->request->getUrl()))
                return Yii::$app->redirects->check(Yii::$app->request->getUrl());
        }

        // If the language is not transmitted, a resource with the default language may be requested
        if (is_null($lang) && !is_null($this->_lang))
            $lang = $this->_lang;

        // Search model with alias and lang
        $model = $this->findModel($alias, $lang, $draft);
        $route = $model->getRoute();

        // Separate route from request URL
        if (is_null($route) && preg_match('/^([\/]+[A-Za-z0-9_\-\_\/]+[\/])*([A-Za-z0-9_\-\_]*)/i', Yii::$app->request->url,$matches)) {
            if ($alias == $matches[2])
                $route = rtrim($matches[1], '/');
        }

        // If route is root
        if (empty($route))
            $route = '/';

        return $this->render('view', [
            'route' => $route,
            'model' => $model
        ]);
    }

    /**
     * Finds the News model based on alias value and language locale.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param $alias
     * @param null $lang
     * @param bool $draft
     * @return array|\modules\base\models\ActiveRecord[]
     * @throws NotFoundHttpException
     */
    protected function findModel($alias, $lang = null, $draft = false)
    {
        $locale = null;
        if (!is_null($lang)) {
            $locales = [];
            if (isset(Yii::$app->translations) && class_exists('modules\translations\models\Languages')) {
                $locales = Yii::$app->translations->getLocales(true, true, true);
                $locales = ArrayHelper::map($locales, 'url', 'locale');
            } else {
                if (is_array($this->module->supportLocales)) {
                    $supportLocales = $this->module->supportLocales;
                    foreach ($supportLocales as $locale) {
                        if ($lang === \Locale::getPrimaryLanguage($locale)) {
                            $locales[$lang] = $locale;
                            break;
                        }
                    }
                }
            }
            if (isset($locales[$lang])) {
                $locale = $locales[$lang];
            }
        }

        // Throw an exception if a news post with a language locale was requested,
        // which is unavailable or disabled for display in the frontend
        if ((!$draft) && !is_null($lang) && is_null($locale)) {
            throw new NotFoundHttpException(Yii::t('app/modules/news', 'The requested news post does not exist.'));
        }

        if (!$draft) {
            $cond = [
                'alias' => $alias,
                'locale' => ($locale) ? $locale : null,
                'status' => News::STATUS_PUBLISHED,
            ];
        } else {
            $cond = [
                'alias' => $alias,
                'status' => News::STATUS_DRAFT,
            ];
        }

        if (($model = News::findOne($cond)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app/modules/news', 'The requested news post does not exist.'));
        }
    }
}
