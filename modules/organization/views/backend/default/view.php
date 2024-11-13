<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \modules\organization\models\Organization */

$this->title = $model->name['ru']; // Отображение на русском языке
$this->params['breadcrumbs'][] = ['label' => 'Организации', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organization-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить эту организацию?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'name',
                'value' => function ($model) {
                    return $model->name['kk'];
                },
            ],
            [
                'attribute' => 'description',
                'value' => function ($model) {
                    return $model->description['kk'] ?? '';
                },
            ],
            'address',
            'status',
            'type',
            'cityId',
            'created_at',
            'updated_at',
        ],
    ]) ?>
</div>

