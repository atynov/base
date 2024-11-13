<?php

/**
 * @var $this yii\web\View
 * @var $model modules\users\models\User
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use modules\users\Module;

$this->title = Module::t('module', 'Users');
$this->params['title']['small'] = Yii::t('app', 'Создать');

$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Создать');
?>

<div class="users-backend-default-create">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Yii::t('app', 'Создать'); ?></h3>
        </div>
        <?php $form = ActiveForm::begin(); ?>
        <div class="box-body">

            <?= $form->field($model, 'username')->textInput([
                'maxlength' => true,
                'placeholder' => true,
            ]) ?>

            <?= $form->field($model, 'email')->textInput([
                'maxlength' => true,
                'placeholder' => true,
            ]) ?>

            <?= $form->field($model, 'password')->passwordInput([
                'maxlength' => true,
                'placeholder' => true,
            ]) ?>

            <?= $form->field($model, 'status')->dropDownList($model->statusesArray) ?>



            <?= $form->field($model, 'organization_id')->dropDownList($organizations, [
                'prompt' => Yii::t('app', 'Выберите организацию')
            ]) ?>

        </div>
        <div class="box-footer">
            <div class="form-group">
                <?= Html::submitButton('<span class="fa fa-floppy-o"></span> ' . Yii::t('app', 'Создать'), [
                    'class' => 'btn btn-success',
                    'name' => 'submit-button',
                ]) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
