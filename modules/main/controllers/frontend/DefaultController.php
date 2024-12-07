<?php

namespace modules\main\controllers\frontend;

use frontend\controllers\FrontendController;
use modules\main\models\forms\SearchForm;
use modules\organization\models\File;
use modules\reports\models\Direction;
use modules\reports\models\Report;
use Yii;
use yii\web\ErrorAction;
use yii\captcha\CaptchaAction;
use yii\web\Response;
use modules\main\models\frontend\ContactForm;
use modules\users\models\User;
use modules\main\Module;

/**
 * Class DefaultController
 * @package modules\main\controllers\frontend
 */
class DefaultController extends FrontendController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['error', 'about', 'contact', 'search'], // Публичные действия
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'send'], // Действия только для авторизованных
                        'allow' => true,
                        'roles' => ['@'], // @ означает авторизованных пользователей
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'backColor' => 0xF1F1F1,
                'foreColor' => 0xEE7600
            ]
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            $this->redirect(['users/default/login']);
        }
        $query = Report::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->orderBy(['start_date' => SORT_DESC, 'id' => SORT_DESC]); // Сортировка по start_date DESC, затем по id DESC

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10, // Количество отчетов на странице
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = Report::findOne($id);
        $files = File::find()
            ->where(['target_id' => $id])
            ->andWhere(['table' => 'reports'])
            ->all();

        return $this->render('view', [
            'model' => $model,
            'files' => $files,
        ]);
    }

    protected function removeOldImages($organizationId, $uploadedUrls)
    {
        File::deleteAll([
            'and',
            ['target_id' => $organizationId],
            ['table' => 'reports'],
            ['not in', 'url', $uploadedUrls]
        ]);
    }

    public function actionUpdate($id)
    {
        $model = Report::findOne($id);
        if ($model->send_status == 0) {
            $directions = Direction::find()->all();
            // Получаем существующие изображения
            $existingImages = File::find()
                ->select(['url'])
                ->where(['target_id' => $id])
                ->andWhere(['table' => $model::tableName()])
                ->column();

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                // Обработка новых загруженных изображений
                $uploadedUrls = explode(',',Yii::$app->request->post('uploadedImageUrls'));
                $deletedImageUrls =  explode(',',Yii::$app->request->post('deletedImageUrls'));
                $uploadedUrls =  array_diff($uploadedUrls, $deletedImageUrls);

                $this->removeOldImages($model->id, $uploadedUrls);
                foreach ($uploadedUrls as $url) {
                    if (!in_array($url, $existingImages)) {
                        $file = new File();
                        $file->url = $url;
                        $file->table = 'reports';
                        $file->target_id = $model->id;
                        $file->save();
                    }
                }

                Yii::$app->session->setFlash('success', 'Есеп сәтті өзгертілді!');
                return $this->redirect(['index']);
            }

            return $this->render('update', [
                'model' => $model,
                'directions' => $directions,
                'existingImages' => $existingImages,
            ]);
        } else {
            $this->redirect(['view', 'id'=>$id]);
        }

    }

    public function actionSend($id, $return )
    {
        $model = Report::findOne($id);
        if ($model) {
            $model->send_status = 1;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Есеп сәтті жіберілді!');
            }
        }
        if ($return =='view') {
            return $this->redirect([$return, 'id'=>$id]);
        }
        return $this->redirect($return);
    }

    public function actionCreate()
    {
        $model = new Report();
        $directions = Direction::find()->all(); // Получение направлений
        if (empty($model->start_date)) {
            $model->start_date = date('Y-m-d');
        }
        if (empty($model->end_date)) {
            $model->end_date = date('Y-m-d');
        }

        if (empty($model->status)) {
            $model->status = 2;
        }
        $model->load(Yii::$app->request->post());

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
            Yii::$app->session->setFlash('success', 'Есеп сәтті қосылды!');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'directions' => $directions,
        ]);
    }

    /**
     * Displays contact page.
     *
     * @return mixed|Response
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if (Yii::$app->user->isGuest) {
            $model->scenario = $model::SCENARIO_GUEST;
        } else {
            $user = Yii::$app->user;
            /** @var User $identity */
            $identity = $user->identity;
            $model->name = $identity->username;
            $model->email = $identity->email;
        }
        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            if ($model->validate()) {
                return $this->processSendEmail($model);
            }
        }
        return $this->render('contact', [
            'model' => $model
        ]);
    }

    /**
     * @param ContactForm $model
     * @return Response
     */
    protected function processSendEmail($model)
    {
        /** @var yii\web\Session $session */
        $session = Yii::$app->session;
        if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
            $session->setFlash('success', Module::t('module', 'Thank you for contacting us. We will respond to you as soon as possible.'));
        } else {
            $session->setFlash('error', Module::t('module', 'There was an error sending email.'));
        }
        return $this->refresh();
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSearch()
    {
        $searchModel = new SearchForm();
        $data = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render("search", [
            'searchModel' => $searchModel,
            'provider' => $data['provider'],
            'rows' => $data['rows'],
            'models' => $data['models']
        ]);
    }

}
