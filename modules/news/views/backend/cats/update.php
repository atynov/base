<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\news\models\NewsCategory */

$this->title = Yii::t('app/modules/news', 'Updating category: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/news', 'news library'), 'url' => ['list/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/news', 'All categories'), 'url' => ['cats/index']];
$this->params['breadcrumbs'][] = Yii::t('app/modules/news', 'Edit');

?>
<?php if (Yii::$app->authManager  && Yii::$app->user->can('managerPosts', [
        'created_by' => $model->created_by,
        'updated_by' => $model->updated_by
    ])) : ?>

    <div class="news-cats-update">
        <?= $this->render('_form', [
            'module' => $module,
            'model' => $model,
            'parentsList' => $model->getParentsList(false, true)
        ]); ?>
    </div>
<?php else : ?>

    <div class="news-cats-update-error">
        <blockquote>
            <?= Yii::t('app/modules/news', 'You are not allowed to view this page.'); ?>
        </blockquote>
    </div>
<?php endif; ?>
