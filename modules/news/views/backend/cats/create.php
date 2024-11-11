<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\news\models\NewsCategory */

$this->title = Yii::t('app/modules/news', 'New category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/news', 'news library'), 'url' => ['list/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/news', 'All categories'), 'url' => ['cats/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="news-cats-create">
    <?= $this->render('_form', [
        'module' => $module,
        'model' => $model,
        'parentsList' => $model->getParentsList(false, true)
    ]); ?>
</div>
