<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\organization\models\Categories */

$this->title = Yii::t('app/modules/organization', 'New type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/organization', 'Organization library'), 'url' => ['list/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/organization', 'All types'), 'url' => ['types/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organization-types-create">
    <?= $this->render('_form', [
        'module' => $module,
        'model' => $model,
        'parentsList' => $model->getParentsList(false, true)
    ]); ?>
</div>
