<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\organization\models\Organization */

$this->title = 'Мешітті қосу';
$this->params['breadcrumbs'][] = ['label' => 'Мешіттер тізімі', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organization-create">

    <?= $this->render('_form', [
        'model' => $model,
        'existingImages' => [],
        'cities' => $cities
    ]) ?>

</div>
