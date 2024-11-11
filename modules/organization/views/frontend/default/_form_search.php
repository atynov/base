<?php
/**
 * Created by PhpStorm.
 * User: Almas
 * Date: 23.04.2019
 * Time: 15:36
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use modules\organization\components\CategoryRepository;
use modules\users\Module;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */


$this->registerJs(
    '$("document").ready(function(){ 
		$("#search_panel").on("pjax:end", function() {
			$.pjax.reload({container:"#records"});
		});
    });'
);
?>



<div class="records-form">
    <?php yii\widgets\Pjax::begin(['id' => 'search_panel']) ?>
    <?php $form = ActiveForm::begin([
        'action' => ['default/index'],
        'options' => ['data-pjax' => true ],
        'method' => 'get',
        ]); ?>

    <div class="row" id="search">
            <div class="form-group col-xs-9">
                <?= $form->field($model, 'keyword')->textInput(['maxlength' => 200])->label(false) ?>
            </div>
            <div class="form-group col-xs-3">
                <?= Html::submitButton(\Yii::t('app/modules/organization', 'Найти'), ['class' => 'btn btn-primary']) ?>
            </div>
    </div>
    <div class="row" id="filter">
            <div class="form-group col-sm-3 col-xs-6">

            </div>

        <div class="form-group col-sm-2 col-xs-6">
        </div>

        <?php ActiveForm::end(); ?>
        <?php yii\widgets\Pjax::end() ?>
    </div>


</div>
