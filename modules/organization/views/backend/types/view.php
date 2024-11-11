<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\organization\models\Categories */

$this->title = Yii::t('app/modules/organization', 'View type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/organization', 'Organization library'), 'url' => ['list/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/organization', 'All types'), 'url' => ['types/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="organization-types-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($model) {
                    $output = Html::tag('strong', $model->name);
                    if (($typeURL = $model->getCategoryUrl(true, true)) && $model->id) {
                        $output .= '<br/>' . Html::a($model->getCategoryUrl(true, false), $typeURL, [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    }
                    return $output;
                }
            ],
            'title:ntext',
            'description:ntext',
            'keywords:ntext',

            [
                'attribute' => 'organization',
                'label' => Yii::t('app/modules/organization', 'Organization items'),
                'format' => 'html',
                'value' => function($data) {
                    if ($organization = $data->organization) {
                        return Html::a(count($organization), ['list/index', 'cat_id' => $data->id]);
                    } else {
                        return 0;
                    }
                }
            ],
            [
                'attribute' => 'created',
                'label' => Yii::t('app/modules/organization','Created'),
                'format' => 'html',
                'value' => function($data) {

                    $output = "";
                    if ($user = $data->createdBy) {
                        $output = Html::a($user->username, ['../admin/users/view/?id='.$user->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    } else if ($data->created_by) {
                        $output = $data->created_by;
                    }

                    if (!empty($output))
                        $output .= ", ";

                    $output .= Yii::$app->formatter->format($data->updated_at, 'datetime');
                    return $output;
                }
            ],
            [
                'attribute' => 'updated',
                'label' => Yii::t('app/modules/organization','Updated'),
                'format' => 'html',
                'value' => function($data) {

                    $output = "";
                    if ($user = $data->updatedBy) {
                        $output = Html::a($user->username, ['../admin/users/view/?id='.$user->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    } else if ($data->updated_by) {
                        $output = $data->updated_by;
                    }

                    if (!empty($output))
                        $output .= ", ";

                    $output .= Yii::$app->formatter->format($data->updated_at, 'datetime');
                    return $output;
                }
            ],

        ],
    ]); ?>
    <hr/>
    <div class="form-group">
        <?= Html::a(Yii::t('app/modules/organization', '&larr; Back to list'), ['types/index'], ['class' => 'btn btn-default pull-left']) ?>&nbsp;
        <?php if (Yii::$app->authManager  && Yii::$app->user->can('managerPosts', [
                'created_by' => $model->created_by,
                'updated_by' => $model->updated_by
            ])) : ?>
            <?= Html::a(Yii::t('app/modules/organization', 'Update'), ['types/update', 'id' => $model->id], ['class' => 'btn btn-primary pull-right']) ?>
        <?php endif; ?>
    </div>
</div>
