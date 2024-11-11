<?php

namespace backend\controllers;

use modules\rbac\models\Permission;
use modules\users\models\User;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Site controller
 */
class DefaultController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        /** @var yii\web\User $user */
        $user = Yii::$app->user;
        if (!$user->can(Permission::PERMISSION_VIEW_ADMIN_PAGE)) {
            /** @var yii\web\Session $session */
//            $session = Yii::$app->session;
//            $session->setFlash('error', Yii::t('app', 'You are not allowed access!'));
            return $this->goHome();
        }
        //Greeting in the admin panel :)
        /** @var User $identity */
        $identity = Yii::$app->user->identity;
        /** @var yii\web\Session $session */
//        $session = Yii::$app->session;
//        $session->setFlash('info', Yii::t('app', 'Welcome, {:username}!', [
//            ':username' => $identity->username
//        ]));

        return $this->render('index');
    }
}
