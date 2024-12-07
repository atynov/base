<?php

/**
 * @var $this yii\web\View
 * @var $model modules\users\models\User
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use modules\users\Module;

$this->title = Module::t('module', 'Update');
$this->params['title']['small'] = $model->username;
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Module::t('module', 'Update');
?>

<div class="users-backend-default-update">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($model->username); ?></h3>
        </div>
        <?php $form = ActiveForm::begin(); ?>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group icon-input">
                        <?= $form->field($model, 'username')->textInput([
                            'placeholder' => '7__________',
                            'class' => 'form-control phone-input',
                            'data-inputmask' => "'mask': '7 799 999 99 99'"
                        ]) ?>
                    </div>

                    <?= $form->field($model, 'password')->passwordInput([
                        'maxlength' => true,
                        'placeholder' => true,
                    ]) ?>

                    <?= $form->field($model, 'directions')->widget(\kartik\select2\Select2::class, [
                        'data' => \modules\reports\models\Direction::getList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Выберите направления'),
                            'multiple' => true, // Включаем мультивыбор
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]) ?>


                    <?= $form->field($model, 'organization_id')->dropDownList($organizations, [
                        'prompt' => Yii::t('app', 'Выберите организацию')
                    ]) ?>

                    <hr>

                    <?= $form->field($model->profile, 'first_name')->textInput([
                        'maxlength' => true,
                        'placeholder' => true,
                    ]) ?>


                </div>
            </div>
        </div>
        <div class="box-footer">
            <div class="form-group">
                <?= Html::submitButton('<span class="fa fa-floppy-o"></span> ' . Module::t('module', 'Сақтау'), [
                    'class' => 'btn btn-primary',
                    'name' => 'submit-button',
                ]) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
$this->registerJs(<<<JS
    $(document).ready(function(){
        $('.phone-input').inputmask({
            mask: '7 799 999 99 99',
            placeholder: '_',
            clearMaskOnLostFocus: false
        });
    });
JS
);
?>