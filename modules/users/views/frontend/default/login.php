<?php

/**
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \modules\users\models\LoginForm
 */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use modules\users\Module;

$this->title = Module::t('module', 'Кіру');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->registerCss("
    .login-container {
        max-width: 400px;
        margin: 50px auto;
        padding: 20px;
        border-radius: 10px;
        background: #f8f9fa;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    .login-title {
        text-align: center;
        margin-bottom: 20px;
        color: #343a40;
    }
    .form-control {
        border-radius: 20px;
    }
    .btn-primary {
        border-radius: 20px;
        background-color: #007bff;
        border: none;
        transition: background-color 0.3s ease;
    }
    .btn-primary:hover {
        background-color: #0056b3;
    }
    .forgot-password {
        display: block;
        text-align: center;
        margin-top: 10px;
        color: #6c757d;
    }
    .forgot-password:hover {
        color: #007bff;
    }
    .icon-input {
        position: relative;
    }
    .icon-input input {
        padding-left: 40px;
    }
    .icon-input .fa {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
    .help-block {
    display: none;
    }
");
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<div class="users-frontend-default-login">
    <div class="login-container">
        <h1 class="login-title"><?= Html::encode($this->title) ?></h1>

        <p class="text-center"><?= Module::t('module', 'Сайтқа кіру үшін логин мен құпиясөзді енгізіңіз'); ?></p>

        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

        <div class="form-group icon-input">
            <i class="fa fa-phone"></i>
            <?= $form->field($model, 'username')->textInput([
                'placeholder' => '7 ___ ___ __ __',
                'class' => 'form-control phone-input',
                'data-inputmask' => "'mask': '7 799 999 99 99'"
            ])->label(false) ?>
        </div>

        <div class="form-group icon-input">
            <i class="fa fa-lock"></i>
            <?= $form->field($model, 'password')->passwordInput([
                'placeholder' => Module::t('module', 'Құпия сөз'),
                'class' => 'form-control'
            ])->label(false) ?>
        </div>

        <?= $form->field($model, 'rememberMe')->checkbox(['class' => 'form-check-input'])->label(Module::t('module', 'Мені есте сақта')) ?>

        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-sign-in-alt"></i> ' . Module::t('module', 'Кіру'), [
                'class' => 'btn btn-primary btn-block',
                'name' => 'login-button'
            ]) ?>
        </div>

<!--        <a href="--><?php //= yii\helpers\Url::to(['default/request-password-reset']) ?><!--" class="forgot-password">-->
<!--            --><?php //= Module::t('module', 'Құпия сөзді ұмыттыңыз ба?') ?>
<!--        </a>-->

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
