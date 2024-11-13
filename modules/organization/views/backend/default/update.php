<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\organization\models\Organization */

$this->title =  $model->name['kk'];
$this->params['breadcrumbs'][] = ['label' => 'Мешіттер', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name['kk'], 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Жазбаны жаңарту';
?>
<div class="organization-update">

    <?= $this->render('_form', [
        'model' => $model,
        'existingImages' => $existingImages,
        'cities' => $cities
    ]) ?>

</div>
