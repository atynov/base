<?php

/**
 * @var $this yii\web\View
 * @var $model modules\users\models\User
 * @var $assignModel \modules\rbac\models\Assignment
 */

use modules\users\Module;
use yii\bootstrap\Tabs;
use yii\helpers\Html;

$this->title = Module::t('module', 'Көру');
$this->params['title']['small'] = $model->username;

$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Қолданушылар'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Module::t('module', 'Көру');

?>

<div class="users-backend-default-view">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($model->username); ?></h3>
        </div>
        <div class="nav-tabs-custom">
            <?= Tabs::widget([
                'items' => [
                    [
                        'label' => Module::t('module', 'Профиль'),
                        'content' => $this->render('tabs/_view_profile', [
                            'model' => $model,
                            'assignModel' => $assignModel,
                        ]),
                        'options' => ['id' => 'profile'],
                        'active' => (!Yii::$app->request->get('tab') || (Yii::$app->request->get('tab') == 'profile')) ? true : false,
                    ],
                ]
            ]); ?>
        </div>
    </div>
</div>
