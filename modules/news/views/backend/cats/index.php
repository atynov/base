<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\widgets\SelectInput\SelectInput;

/* @var $this yii\web\View */
/* @var $model modules\news\models\NewsCategory */

// $bundle = \modules\news\newsAsset::register($this);

$this->title = Yii::t('app/modules/news', 'News categories');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/news', 'news library'), 'url' => ['list/index']];
$this->params['breadcrumbs'][] = Yii::t('app/modules/news', 'All categories');

?>

<div class="news-cats-index">

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($model) {
                    $output = Html::tag('strong', $model->name);
                    $output .= (($model->id === 1) ? " <span class=\"text-muted\">(" . Yii::t('app/modules/news', 'default') . ")</span>" : "");
                    if (($categoryURL = $model->getCategoryUrl(true, true)) && $model->id) {
                        $output .= '<br/>' . Html::a($model->getCategoryUrl(true, false), $categoryURL, [
                                'target' => '_blank',
                                'data-pjax' => 0
                            ]);
                    }
                    return $output;
                }
            ],
            [
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function($model) {
                    $output = mb_strimwidth(strip_tags($model->title), 0, 64, '…');

                    if (mb_strlen($model->title) > 81)
                        $output .= '&nbsp;' . Html::tag('span', Html::tag('span', '', [
                                'class' => 'fa fa-fw fa-exclamation-triangle',
                                'title' => Yii::t('app/modules/news','Field exceeds the recommended length of {length} characters.', [
                                    'length' => 80
                                ])
                            ]), ['class' => 'label label-warning']);

                    return $output;
                }
            ],
            [
                'attribute' => 'description',
                'format' => 'raw',
                'value' => function($model) {
                    if (!empty($model->description)) {
                        $output = mb_strimwidth(strip_tags($model->description), 0, 64, '…');

                        if (mb_strlen($model->description) > 161)
                            $output .= '&nbsp;' . Html::tag('span', Html::tag('span', '', [
                                    'class' => 'fa fa-fw fa-exclamation-triangle',
                                    'title' => Yii::t('app/modules/news','Field exceeds the recommended length of {length} characters.', [
                                        'length' => 160
                                    ])
                                ]), ['class' => 'label label-warning']);

                        return $output;
                    }
                }
            ],
            [
                'attribute' => 'keywords',
                'format' => 'raw',
                'value' => function($model) {
                    if (!empty($model->keywords)) {
                        $output = mb_strimwidth(strip_tags($model->keywords), 0, 64, '…');

                        if (mb_strlen($model->keywords) > 181)
                            $output .= '&nbsp;' . Html::tag('span', Html::tag('span', '', [
                                    'class' => 'fa fa-fw fa-exclamation-triangle',
                                    'title' => Yii::t('app/modules/news','Field exceeds the recommended length of {length} characters.', [
                                        'length' => 180
                                    ])
                                ]), ['class' => 'label label-warning']);

                        return $output;
                    }
                }
            ],

            [
                'attribute' => 'news',
                'label' => Yii::t('app/modules/news', 'Items'),
                'format' => 'html',
                'value' => function($data) {
                    if ($news = $data->news) {
                        return Html::a(count($news), ['list/index', 'category_id' => $data->id]);
                    } else {
                        return 0;
                    }
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app/modules/news','Actions'),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'visibleButtons' => [
                    'update' => function ($model) {
                        if (Yii::$app->authManager  && !Yii::$app->user->can('managerPosts', [
                                'created_by' => $model->created_by,
                                'updated_by' => $model->updated_by
                            ])) {
                            return false;
                        }

                        return true;
                    },
                    'delete' => function ($model) {
                        // Category for uncategorized posts has undeleted
                        if ($model->id === $model::DEFAULT_CATEGORY_ID) {
                            return false;
                        } else {
                            if (Yii::$app->authManager  && !Yii::$app->user->can('managerPosts', [
                                    'created_by' => $model->created_by,
                                    'updated_by' => $model->updated_by
                                ])) {
                                return false;
                            }

                            return true;
                        }
                    },
                ],
            ]
        ],
        'pager' => [
            'options' => [
                'class' => 'pagination',
            ],
            'maxButtonCount' => 5,
            'activePageCssClass' => 'active',
            'prevPageCssClass' => 'prev',
            'nextPageCssClass' => 'next',
            'firstPageCssClass' => 'first',
            'lastPageCssClass' => 'last',
            'firstPageLabel' => Yii::t('app/modules/news', 'First page'),
            'lastPageLabel'  => Yii::t('app/modules/news', 'Last page'),
            'prevPageLabel'  => Yii::t('app/modules/news', '&larr; Prev page'),
            'nextPageLabel'  => Yii::t('app/modules/news', 'Next page &rarr;')
        ],
    ]); ?>
    <hr/>
    <div>
        <?= Html::a(Yii::t('app/modules/news', 'Add new category'), ['cats/create'], ['class' => 'btn btn-success pull-right']) ?>
    </div>
    <?php Pjax::end(); ?>
</div>
