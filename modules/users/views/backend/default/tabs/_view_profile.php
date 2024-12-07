<?php

/**
 * @var $this yii\web\View
 * @var $model modules\users\models\User
 * @var $assignModel \modules\rbac\models\Assignment
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use modules\users\widgets\AvatarWidget;
use modules\users\assets\UserAsset;
use modules\users\Module;

UserAsset::register($this);

?>

<div class="row">
    <div class="col-sm-2">
        <?= AvatarWidget::widget([
            'email' => $model->profile->email_gravatar,
            'imageOptions' => [
                'class' => 'profile-user-img img-responsive img-circle',
                'style' => 'margin-bottom:10px; width:auto',
                'alt' => 'avatar_' . $model->username,
            ]
        ]) ?>
    </div>
    <div class="col-sm-10">
        <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table table-bordered detail-view'],
            'attributes' => [
                'id',
                [
                    'attribute' => 'username',
                    'label' => 'Қолданушы аты',
                ],
                [
                    'attribute' => 'profile.first_name',
                    'label' => 'Аты-жөні',
                ],
                [
                    'attribute' => 'userRoleName',
                    'label' => 'Лауазымы',
                    'format' => 'raw',
                    'value' => function ($model) use ($assignModel) {
                        return $assignModel->getRoleName($model->id);
                    },
                ],
                [
                    'attribute' => 'password',
                    'label' => 'Құпия сөз',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $hiddenPassword = str_repeat('*', 8);
                        return '<div class="password-field">
                    <span class="password-text">' . $hiddenPassword . '</span>
                    <button type="button" class="btn btn-link toggle-password" data-password="' . $model->password_reset_token . '">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>';
                    },
                ],
                [
                    'attribute' => 'created_at',
                    'label' => 'Құрылған күні',
                    'format' => 'raw',
                    'value' => Yii::$app->formatter->asDatetime($model->created_at, 'd LLL yyyy, H:mm:ss'),
                ],
                [
                    'attribute' => 'updated_at',
                    'label' => 'Жаңартылған күні',
                    'format' => 'raw',
                    'value' => Yii::$app->formatter->asDatetime($model->updated_at, 'd LLL yyyy, H:mm:ss'),
                ],
                [
                    'attribute' => 'profile.last_visit',
                    'label' => 'Соңғы кіру',
                    'format' => 'raw',
                    'value' => Yii::$app->formatter->asDatetime($model->profile->last_visit, 'd LLL yyyy, H:mm:ss'),
                ],
            ],
        ]) ?>
    </div>

    <div class="col-sm-offset-2 col-sm-10">
        <?= Html::a('<i class="fa fa-edit"></i> Өңдеу', ['update', 'id' => $model->id], [
            'class' => 'btn btn-primary',
        ]) ?>

        <?= Html::a('<i class="fa fa-trash"></i> Жою', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Жазбаны жойғыңыз келе ме?',
                'method' => 'post',
            ],
        ]) ?>
    </div>
</div>

<style>
    .profile-user-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .detail-view {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        background: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .detail-view td {
        border: 1px solid #ddd;
        padding: 12px 15px;
    }

    .detail-view td:first-child {
        background-color: #f8f9fa;
        font-weight: bold;
        width: 200px;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 5px;
        margin-right: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
    }

    .btn-danger:hover {
        background-color: #c82333;
        transform: translateY(-2px);
    }

    .fas {
        margin-right: 5px;
    }
    .password-field {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .toggle-password {
        padding: 0;
        color: #6c757d;
    }

    .toggle-password:hover {
        color: #007bff;
    }
</style>


<?php
$this->registerJs(<<<JS
    $(document).ready(function(){
        $('.toggle-password').on('click', function() {
    var passwordField = $(this).siblings('.password-text');
    var password = $(this).data('password');
    var icon = $(this).find('i');
    
    if (passwordField.text() === '********') {
        passwordField.text(password);
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        passwordField.text('********');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
});

    });
JS
);
?>