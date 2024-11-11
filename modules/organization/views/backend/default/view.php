<?php

/**
 * @var $this yii\web\View
 * @var $model modules\organization\models\Content
 */

use modules\users\Module;
use yii\helpers\Html;

$this->title = \Yii::t('app/modules/organization', 'View');
$this->params['title']['small'] = $model->name;

$this->params['breadcrumbs'][] = ['label' => \Yii::t('app/modules/organization', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = \Yii::t('app/modules/organization', 'View');

?>

<div class="users-backend-default-view">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($model->name); ?></h3>
        </div>
        <div >
            <?= $model->text; ?>

        </div>
        <h3><?= \Yii::t('app/modules/organization', 'Список пользователей') ?></h3>

        Таблица: ФИО Роль Действия
    </div>
</div>
