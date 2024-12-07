<?php /** * @var $this yii\web\View * @var $model modules\users\models\User */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use modules\users\Module;
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->title = Module::t('module', 'Қолданушылар');
$this->params['title']['small'] = Yii::t('app', 'Қосу');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Қолданушылар'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Қосу'); ?>
<div class="users-backend-default-create">
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('app', 'Қосу'); ?></h3>
        </div> <?php $form = ActiveForm::begin(); ?>
        <div class="box-body">


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


            <?= $form->field($model, 'organization_id')->dropDownList($organizations, [
                'prompt' => Yii::t('app', 'Ұйымды таңдаңыз')
            ]) ?>

        </div>
        <div class="box-footer">
            <div class="form-group">
                <?= Html::submitButton('<span class="fa fa-floppy-o"></span> ' . Yii::t('app', 'Қосу'), [
                    'class' => 'btn btn-success',
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