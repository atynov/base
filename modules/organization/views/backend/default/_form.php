<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \modules\organization\models\Organization */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="organization-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'id' => 'organization-form',
    ]); ?>

    <?= \common\widgets\ImageUploadWidget::widget([
        'uploadUrl' => ['/admin/file/upload-image'],
        'model' => $model,
        'existingImages' => $existingImages,
        'attribute' => 'imageFiles',
        'submit' => 'submit-button',
        'form' => 'organization-form'
    ]) ?>

    <?= $form->field($model, 'name[kk]')->textInput(['maxlength' => true])->label('Атауы (Қазақша)') ?>
    <?= $form->field($model, 'name[ru]')->textInput(['maxlength' => true])->label('Атауы (Орысша)') ?>
    <?= $form->field($model, 'description[kk]')->textarea(['rows' => 4])->label('Сипаттамасы (Қазақша)') ?>
    <?= $form->field($model, 'description[ru]')->textarea(['rows' => 4])->label('Сипаттамасы (Орысша)') ?>
    <?= $form->field($model, 'address')->textInput(['maxlength' => true])->label('Мекенжай') ?>
    <?= $form->field($model, 'status')->dropDownList([1 => 'Белсенді', 0 => 'Белсенді емес'], [
        'prompt' => 'Статусты таңдаңыз',
        'value' => $model->isNewRecord ? 1 : $model->status, // Установка значения 1 для новых записей
    ]) ?>
    <?= $form->field($model, 'cityId')->dropDownList($cities, ['prompt' => 'Елді мекенді таңдаңыз'])->label('Елді мекен') ?>

    <div class="form-group">
        <?= Html::button($model->isNewRecord ? 'Қосу' : 'Сақтау', [
            'class' => 'btn btn-success',
            'id' => 'submit-button'
        ]) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
