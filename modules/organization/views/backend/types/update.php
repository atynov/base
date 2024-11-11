<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\organization\models\Categories */

$this->title = Yii::t('app/modules/organization', 'Updating type: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/organization', 'Organization library'), 'url' => ['list/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/organization', 'All types'), 'url' => ['types/index']];
$this->params['breadcrumbs'][] = Yii::t('app/modules/organization', 'Edit');

?>
<?php if (Yii::$app->authManager  && Yii::$app->user->can('managerPosts', [
        'created_by' => $model->created_by,
        'updated_by' => $model->updated_by
    ])) : ?>
    <div class="organization-types-update">
        <?= $this->render('_form', [
            'module' => $module,
            'model' => $model,
            'parentsList' => $model->getParentsList(false, true)
        ]); ?>
    </div>
<?php else : ?>
    <div class="page-header">
        <h1 class="text-danger"><?= Yii::t('app/modules/organization', 'Error {code}. Access Denied', [
                'code' => 403
            ]) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
    </div>
    <div class="organization-types-update-error">
        <blockquote>
            <?= Yii::t('app/modules/organization', 'You are not allowed to view this page.'); ?>
        </blockquote>
    </div>
<?php endif; ?>
