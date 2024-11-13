<?php

namespace modules\organization\controllers\backend;

use backend\controllers\BackendController;
use modules\directory\enums\DicValueTypeEnum;
use modules\directory\models\DicValues;
use modules\organization\models\File;
use modules\organization\models\Organization;
use modules\rbac\models\Role;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class DefaultController
 * @package modules\organization\controllers\backend
 */
class DefaultController extends BackendController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Organization::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Organization(); // Основная модель
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $uploadedImageUrls = Yii::$app->request->post('uploadedImageUrls', '');
            $uploadedUrls = explode(',', $uploadedImageUrls);
            foreach ($uploadedUrls as $url) {
                $file = new \modules\organization\models\File();
                $file->url = $url;
                $file->target_id = $model->id; // ID основной записи
                $file->table = $model::tableName(); // Название таблицы
                $file->created_at = date('Y-m-d H:i:s');
                if (!$file->save()) {
                    Yii::error("Ошибка при сохранении файла: " . json_encode($file->errors));
                }
            }

            return $this->redirect(['index']);
        }
        $language = Yii::$app->language;
        return $this->render('create', [
            'model' => $model,
            'existingImages' => [],
            'cities' => DicValues::getCitiesList($language),
        ]);
    }

    protected function removeOldImages($organizationId, $uploadedUrls)
    {
        File::deleteAll([
            'and',
            ['target_id' => $organizationId],
            ['table' => 'organization'],
            ['not in', 'url', $uploadedUrls]
        ]);
    }
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $existingImages = [];
        if ($model->files) {
            foreach ($model->files as $file) {
                $existingImages[] = $file->url;
            }
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $uploadedUrls = explode(',',Yii::$app->request->post('uploadedImageUrls'));
            $deletedImageUrls =  explode(',',Yii::$app->request->post('deletedImageUrls'));
            $uploadedUrls =  array_diff($uploadedUrls, $deletedImageUrls);
            $this->removeOldImages($model->id, $uploadedUrls);
            foreach ($uploadedUrls as $url) {
                if (!in_array($url, $existingImages)) {
                    $file = new File();
                    $file->url = $url;
                    $file->table = 'organization';
                    $file->target_id = $model->id;
                    $file->save();
                }
            }
            return $this->redirect(['index']);
        }
        $language = Yii::$app->language;
        return $this->render('update', [
            'model' => $model,
            'existingImages' => $existingImages,
            'cities' => DicValues::getCitiesList($language),
        ]);
    }


    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Organization::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['create', 'delete'],
                        'roles' => [
                            Role::ROLE_SUPER_ADMIN
                        ],
                        'allow' => true
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'update'],
                        'roles' => [
                            Role::ROLE_SUPER_ADMIN,
                            Role::ROLE_ADMIN
                        ],
                    ],
                ],
            ]
        ];
    }

}
