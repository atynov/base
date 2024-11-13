<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Мешіттер';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organization-index">
    <p>
        <?= Html::a('Мешітті қосу', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'value' => function ($model) {
                    return $model->name['kk']; // Отображение на русском языке
                },
            ],
            'address',
            [
                'attribute' => 'cityId',
                'label' => 'Елді мекен', // Установите нужную метку
                'value' => function ($model) {
                    return $model->city ? $model->city->name['kk'] : null; // Отображает название города, если отношение установлено
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}', // Показывает только кнопки "редактировать" и "удалить"
            ],

        ],
    ]); ?>
</div>
