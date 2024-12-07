<?php
use yii\widgets\DetailView;

echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'name',
        'organization_id',
        'start_date',
        'end_date',
        'people_count',
        'link',
        'description',
        'status',
        'user_id',
    ],
]);
