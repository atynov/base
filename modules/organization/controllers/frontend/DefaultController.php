<?php
namespace modules\organization\controllers\frontend;

use frontend\controllers\FrontendController;
use modules\organization\models\Organization;
use modules\organization\models\OrganizationCategory;
use modules\organization\models\search\OrganizationSearch;
use Yii;
use yii\web\NotFoundHttpException;
use common\models\UploadForm;
use yii\web\UploadedFile;
use yii\data\ArrayDataProvider;

/**
 * Class DefaultController
 * @package modules\organization\controllers\frontend
 */
class DefaultController extends FrontendController
{
    /**
     * @inheritdoc
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new OrganizationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->isPjax) {
            return $this->renderPartial('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }


    public function actionList($subalias)
    {
        $parent = OrganizationCategory::findOne(['alias' => $subalias]);
        $models = Organization::find()->andWhere([
            'category_id' => $parent->id
        ])->all();

        return $this->render('list', [
            'parent' => $parent,
            'models' => $models
        ]);
    }


    /**
     * @param string|int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {

        $model = $this->findModel($id);

        // проверяем не является аукцион предложением о СП
        if (!empty($model->parent_id)) {
            return $this->redirect(['view', 'id' => $model->parent_id]);
        }

        $dpFiles = new ArrayDataProvider([
            'allModels' => $model->fileToContents,
            'sort' => [
                'attributes' => ['filename'],
            ],
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $dpBids = new ArrayDataProvider([
            'allModels' => $model->organizationBids,
            'sort' => [
                'attributes' => ['price_retail', 'create_date'],
                'defaultOrder' => [
                    'price_retail' => SORT_ASC
                ]
            ],
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $dpAucs = new ArrayDataProvider([
            'allModels' => $model->organizations,
            'sort' => [
                'attributes' => ['create_date'],
                'defaultOrder' => [
                    'create_date' => SORT_ASC
                ]
            ],
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $this->render('view', [
            'model' => $model,
            'dpFiles' => $dpFiles,
            'dpBids' => $dpBids,
            'dpAucs' => $dpAucs
        ]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionCreate($parent_id = null)
    {
        $model = new Organization();
        if (!empty($parent_id)) {
            $modelParent = $this->findModel($parent_id);
            $model->attributes = $modelParent->attributes;
            $model->parent_id = $modelParent->id;
        }

        if ($model->load(Yii::$app->request->post('Content'),'')) {
            if ($model->save()) {
                return $this->redirect(['update', 'id' => $model->id, 'fileupload' => true]);
            } else {
                var_dump($model->getErrors());
                exit;
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    public function actionUpdate($id, $fileupload = false)
    {
        $model = $this->findModel($id);

        if (!$model->canUpdate()) {
            throw new NotFoundHttpException(\Yii::t('app/modules/organization', 'The requested page does not exist.'));
        }

        if ($model->load(Yii::$app->request->post(), '')) {

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $model->fileToContents,
            'sort' => [
                'attributes' => ['filename'],
            ],
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $this->render('update', [
            'model' => $model,
            'fileupload' => $fileupload,
            'dataProvider' => $dataProvider
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Organization::findOne($id)) !== null) {
            return $model;
        }
        //throw new NotFoundHttpException(\Yii::t('app/modules/organization', 'The requested page does not exist.'));
    }


    public function actionUpload($id = null)
    {
        $post = new UploadForm();

        if (Yii::$app->request->isPost) {
            $post->files = UploadedFile::getInstances($post, 'files');
            if ($post->upload()) {
                $files = $post->files;
                if (!is_null($files) && is_array($files)) {
                    foreach ($files as $file) {
                        $model = new ContentFile();
                        $model->filename = $file->name;
                        $model->size = $file->size;
                        $model->type = $file->type;
                        if ($model->save()) {
                            if (!empty($id)) {
                                $postFileToContent = new FileToContent();
                                $postFileToContent->file_id = $model->id;
                                $postFileToContent->organization_id = $id;
                                if (!$postFileToContent->save()) {
                                    var_dump ($postFileToContent->getErrors()); die();
                                }
                            }
                        }  else {
                            var_dump ($model->getErrors()); die();
                        }
                    }
                }
                return true;
            }
        }
    }


    public function actionDeleteFile($file_id, $organization_id)
    {
        $modelContent = $this->findModel($organization_id);
        if (!$modelContent->canUpdate()) {
            throw new NotFoundHttpException(\Yii::t('app/modules/organization', 'The requested page does not exist.'));
        }

        $model = FileToContent::find()->where(['file_id' => $file_id, 'organization_id' => $organization_id])->one();
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', \Yii::t('app/modules/organization', 'Файл успешно удален.'));
        }
        return $this->redirect(['update', 'id' => $organization_id]);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->remove();
        return $this->redirect(['my']);
    }
}
