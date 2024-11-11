<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use modules\organization\Module;
use yii\web\JsExpression;
use yii\web\View;


/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app/modules/organization', 'Организации');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app/modules/organization', 'Организации'), 'url' => ['organization/index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->user->id]];
$this->params['breadcrumbs'][] = ($model->scenario == $model::SCENARIO_UPDATE) ? \Yii::t('app/modules/organization', 'Редактировать') : \Yii::t('app/modules/organization', 'Добавить');

?>


<div class="organization-assign-update">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><?= ($model->scenario == $model::SCENARIO_UPDATE) ? \Yii::t('app/modules/organization', 'Редактировать') : \Yii::t('app/modules/organization', 'Добавить') ?>
                <small> <?= \Yii::t('app/modules/organization', 'запись') ?> </small>
            </h3>
        </div>
        <div class="box-body">
            <div class="organization-assign-form">

                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'category_id')->dropDownList($model::getCategoriesList()) ?>

                <?= $form->field($model, 'name')->textInput([
                    'maxlength' => true,
                    'disabled' => ($model->scenario == $model::SCENARIO_UPDATE) ? true : false,
                ])->hint(\Yii::t('app/modules/organization', 'Пример: ...')) ?>


                <?= $form->field($model, 'alias')->textInput([
                    'maxlength' => true,
                    'disabled' => boolval($model->id),
                ]) ?>

                <?php /* $form->field($model, 'alias')->widget(\common\widgets\SubdomainInput::class, [
                    'labels' => [
                        'edit' => Yii::t('app/modules/organization', 'Edit'),
                        'save' => Yii::t('app/modules/organization', 'Save')
                    ],
                    'options' => [
                        'baseUrl' => ($model->id) ? $model->url : \yii\helpers\Url::to($model->getRoute(), true)
                    ]
                ])->label(Yii::t('app/modules/organization', 'Subdomain')); */ ?>


                
                <?= $form->field($model, 'address')->textInput([
                    'maxlength' => true
                ]) ?>

                <?= $form->field($model, 'phone')->textInput([
                    'maxlength' => true,
                    'type' => 'phone'
                ]) ?>

                <?= $form->field($model, 'fax')->textInput([
                    'maxlength' => true,
                    'type' => 'phone'
                ]) ?>

                <?= $form->field($model, 'email')->textInput([
                    'maxlength' => true,
                    'type' => 'email'
                ]) ?>

                <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

                <?= $form->field($model, 'status')->dropDownList($model::getStatusList()) ?>

                <div class="form-group">
                    <?= Html::submitButton('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ' . \Yii::t('app/modules/organization', 'Сохранить'), [
                        'class' => 'btn btn-primary',
                        'name' => 'redirect',
                        'value' => 'create'
                    ]) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>

        </div>
        <div class="box-footer"></div>
    </div>
</div>
