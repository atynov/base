<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use \modules\directory\enums\DicValueTypeEnum;
use \modules\directory\models\DicValues;

/* @var $this yii\web\View */
/* @var $model DicValues */
/* @var $form yii\widgets\ActiveForm */

$language = Yii::$app->language;
$this->title = $model->isNewRecord ? 'Жазбаны қосу' : 'Жазбаны өзгерту';
$regions = ArrayHelper::map(DicValues::find()->where(['type' => DicValueTypeEnum::REGION])->all(), 'id', function($model) use ($language) {
    return $model->name[$language] ?? $model->name['kk'];
});
$districts = ArrayHelper::map(DicValues::find()->where(['type' => DicValueTypeEnum::DISTRICT])->all(), 'id', function($model) use ($language) {
    return $model->name[$language] ?? $model->name['kk'];
});
?>
<h1><?= Html::encode($this->title) ?></h1>

<div class="dic-values-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->dropDownList(DicValueTypeEnum::getTypes($language), [
        'prompt' => 'Түрін таңдаңыз',
        'onchange' => '
            var type = $(this).val();
            if (type == ' . DicValueTypeEnum::DISTRICT . ') {
                $("#region-field").show();
                $("#district-field").hide();
            } else if (type == ' . DicValueTypeEnum::CITY . ') {
                $("#region-field").show();
                $("#district-field").show();
            } else {
                $("#region-field").hide();
                $("#district-field").hide();
            }
        ',
    ])->label('Түрі') ?>

    <div id="region-field" style="display: none;">
        <?= $form->field($model, 'region_id')->dropDownList($regions, ['prompt' => 'Облысты таңдаңыз'])->label('Облыс') ?>
    </div>

    <div id="district-field" style="display: none;">
        <?= $form->field($model, 'district_id')->dropDownList($districts, ['prompt' => 'Ауданды таңдаңыз'])->label('Аудан (міндетті емес)') ?>
    </div>

    <?= $form->field($model, 'name[kk]')->textInput(['maxlength' => true])->label('Атауы (Қазақша)') ?>
    <?= $form->field($model, 'name[ru]')->textInput(['maxlength' => true])->label('Атауы (Орысша)') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Қосу' : 'Жаңарту', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("
    var initialType = $('#dicvalues-type').val();
    if (initialType == " . DicValueTypeEnum::DISTRICT . ") {
        $('#region-field').show();
        $('#district-field').hide();
    } else if (initialType == " . DicValueTypeEnum::CITY . ") {
        $('#region-field').show();
        $('#district-field').show();
    }

    $('#dicvalues-type').on('change', function() {
        var type = $(this).val();
        if (type == " . DicValueTypeEnum::DISTRICT . ") {
            $('#region-field').show();
            $('#district-field').hide();
        } else if (type == " . DicValueTypeEnum::CITY . ") {
            $('#region-field').show();
            $('#district-field').show();
        } else {
            $('#region-field').hide();
            $('#district-field').hide();
        }
    });
");
?>
