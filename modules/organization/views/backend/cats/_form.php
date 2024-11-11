<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\widgets\SelectInput\SelectInput;

/* @var $this yii\web\View */
/* @var $model modules\organization\models\Categories */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="organization-cats-form">
        <?php $form = ActiveForm::begin([
            'id' => "addCategoryForm",
            'enableAjaxValidation' => true,
            'options' => [
                'enctype' => 'multipart/form-data'
            ]
        ]); ?>
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= \common\widgets\MultiLangField\MultiLangField::widget([
            'model' => $model,
            'form' => $form
        ]) ?>

        <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'text')->textarea(['rows' => 3]) ?>

        <?= $form->field($model, 'parent_id')->widget(SelectInput::class, [
            'items' => $parentsList,
            'options' => [
                'class' => 'form-control'
            ]
        ])->label(Yii::t('app/modules/organization', 'Parent category')); ?>
        <hr/>
        <div class="form-group">
            <?= Html::a(Yii::t('app/modules/organization', '&larr; Back to list'), ['cats/index'], ['class' => 'btn btn-default pull-left']) ?>&nbsp;
            <?php if ((Yii::$app->authManager  && Yii::$app->user->can('managerPosts', [
                        'created_by' => $model->created_by,
                        'updated_by' => $model->updated_by
                    ])) || !$model->id) : ?>
                <?= Html::submitButton(Yii::t('app/modules/organization', 'Save'), ['class' => 'btn btn-success pull-right']) ?>
            <?php endif; ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

<?php $this->registerJs(<<< JS
$(document).ready(function() {
    function afterValidateAttribute(event, attribute, messages)
    {
        if (attribute.name && !attribute.alias && messages.length == 0) {
            var form = $(event.target);
            $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: form.serializeArray(),
                }
            ).done(function(data) {
                if (data.alias && form.find('#categories-alias').val().length == 0) {
                    form.find('#categories-alias').val(data.alias);
                    form.yiiActiveForm('validateAttribute', 'categories-alias');
                }
            }).fail(function () {
                /*form.find('#options-type').val("");
                form.find('#options-type').trigger('change');*/
            });
            return false; // prevent default form submission
        }
    }
    $("#addCategoryForm").on("afterValidateAttribute", afterValidateAttribute);
});
JS
); ?>
