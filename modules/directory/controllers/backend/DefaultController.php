<?php

namespace modules\directory\controllers\backend;

use modules\directory\enums\DicValueTypeEnum;
use modules\directory\models\DicValues;
use Yii;
use backend\controllers\BackendController;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements actions for directory model.
 */
class DefaultController extends BackendController
{
    public function actionIndex()
    {
        $items = DicValues::find()->all();
        return $this->render('index', ['items' => $items]);
    }

    public function actionCreate()
    {
        $model = new DicValues();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->type == DicValueTypeEnum::DISTRICT) {
                $model->parent_id = $model->region_id;
            } elseif ($model->type == DicValueTypeEnum::CITY) {
                $model->parent_id = $model->district_id ?: $model->region_id;
            }

            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Инициализация виртуальных атрибутов для формы
        if ($model->type == DicValueTypeEnum::DISTRICT) {
            $model->region_id = $model->parent_id;
        } elseif ($model->type == DicValueTypeEnum::CITY) {
            $model->district_id = $model->parent_id;
            if (!$model->district_id) {
                $model->region_id = $model->parent_id; // Если родитель — это область
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            // Обновление parent_id в зависимости от типа
            if ($model->type == DicValueTypeEnum::DISTRICT) {
                $model->parent_id = $model->region_id;
            } elseif ($model->type == DicValueTypeEnum::CITY) {
                $model->parent_id = $model->district_id ?: $model->region_id;
            }

            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', ['model' => $model]);
    }




    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = DicValues::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
