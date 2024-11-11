<?php

namespace modules\news\controllers\backend;

use backend\controllers\BackendController;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use modules\news\models\News;
use modules\news\models\NewsSearch;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends BackendController
{
    public $modelClass = News::class;

    public $searchClass = NewsSearch::class;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['update', 'create', 'delete'],
                    'roles' => ['managerPosts'],
                    'allow' => true
                ], [
                    'roles' => ['viewAdminPage', 'managerPosts'],
                    'allow' => true
                ],
            ],
        ];

        return $behaviors;
    }

    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return news model item
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested record does not exist.'));
    }


}
