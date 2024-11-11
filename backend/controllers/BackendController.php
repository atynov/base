<?php

namespace backend\controllers;

use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Yii;

/**
 * Class BackendController
 * @package backend\controllers
 */
class BackendController extends Controller
{
    /**
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class
            ]
        ];
    }

    /**
     * @param Action $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if ($action->id === 'error') {
            $this->layout = 'error.php';
        }
        return parent::beforeAction($action);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $modelClass = $this->modelClass;
        $query = $modelClass::find();

        if ($this->searchClass) {
            $searchModel = new $this->searchClass();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    public function actionCreate()
    {
        $model = new $this->modelClass();
        $model->status = $model::STATUS_ACTIVE;
        $model->scenario = $model::SCENARIO_CREATE;

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate())
                    $success = true;
                else
                    $success = false;

                $attributes = [];
                if ($model->hasAttribute('name')) {
                    $attributes['name'] = $model->name;
                }

                if ($model->hasAttribute('alias')) {
                    $attributes['alias'] = $model->alias;
                }

                return $this->asJson(array_merge([
                    'success' => $success,
                    'errors' => $model->errors
                ], $attributes));
            }
        } else {
            if ($model->load(Yii::$app->request->post())) {

                if ($model->validate() && $model->save()) {
                    // Log activity
                    $this->module->logActivity(
                        'Module: `' . $this->module->name . '`; Record ID: `' . $model->id . '` has been successfully added.',
                        $this->uniqueId . ":" . $this->action->id,
                        'success',
                        1
                    );

                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t('app', 'Record has been successfully added!')
                    );

                    return $this->redirect(['index']);

                } else {

                    if (YII_DEBUG) {
                        var_dump($model->getErrors());
                    }

                    // Log activity
                    $this->module->logActivity(
                        'An error occurred while add the new record: ' . $model->name,
                        $this->uniqueId . ":" . $this->action->id,
                        'danger',
                        1
                    );

                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t('app', 'An error occurred while add the new record.')
                    );
                }
            }
        }

        return $this->render('form', [
            'model' => $model,
        ]);
    }

    /**
     * @param int|string $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // No language is set for this model, we will use the current user language
        if ($model->is_translations && is_null($model->locale)) {

            $model->locale = Yii::$app->language;
            if (!Yii::$app->request->isPost) {

                $languages = $model->getLanguagesList(false);
                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t(
                        'app',
                        'No display language has been set. Source language will be selected: {language}',
                        [
                            'language' => (isset($languages[Yii::$app->sourceLanguage])) ? $languages[Yii::$app->sourceLanguage] : Yii::$app->sourceLanguage
                        ]
                    )
                );
            }
        }

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate())
                    $success = true;
                else
                    $success = false;

                $attributes = [];
                if ($model->hasAttribute('name')) {
                    $attributes['name'] = $model->name;
                }

                if ($model->hasAttribute('alias')) {
                    $attributes['alias'] = $model->alias;
                }

                return $this->asJson(array_merge([
                    'success' => $success,
                    'errors' => $model->errors
                ], $attributes));
            }
        } else {
            if ($model->load(Yii::$app->request->post())) {

                if ($model->save()) {

                    // Log activity
                    $this->module->logActivity(
                        'Record `' . $model->name . '` with ID `' . $model->id . '` has been successfully updated.',
                        $this->uniqueId . ":" . $this->action->id,
                        'success',
                        1
                    );

                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t(
                            'app',
                            'Record `{name}` successfully updated.',
                            [
                                'name' => $model->name
                            ]
                        )
                    );
                } else {
                    // Log activity
                    $this->module->logActivity(
                        'An error occurred while update the record `' . $model->name . '` with ID `' . $model->id . '`.',
                        $this->uniqueId . ":" . $this->action->id,
                        'danger',
                        1
                    );

                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t(
                            'app',
                            'An error occurred while add the new record.'
                        )
                    );
                }
                return $this->redirect(['index']);
            }
        }

        return $this->render('form', [
            'model' => $model
        ]);
    }

    public function actionDelete($id)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $result = $this->processDelete($id);
            return [
                'result' => $result->statusLabelName
            ];
        }
        $this->processDelete($id);
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionView($id)
    {
        if ($model = $this->findModel($id)) {

            return $this->render('view', [
                'model' => $model
            ]);
        }
        return $this->redirect(['index']);
    }

    /**
     * @param int|string $id
     * @return array|Response
     * @throws NotFoundHttpException
     */
    public function actionSetStatus($id)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $result = $this->processChangeStatus($id);
            return [
                'result' => $result->statusLabelName
            ];
        }
        $this->processChangeStatus($id);
        return $this->redirect(Yii::$app->request->referrer);
    }
}
