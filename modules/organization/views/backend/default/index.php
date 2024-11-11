<?php

/**
 * @var $this yii\web\View
 * @var $searchModel modules\users\models\search\UserSearch
 * @var $model modules\users\models\User
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $assignModel \modules\rbac\models\Assignment
 */

use modules\person\models\Person;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use modules\users\assets\UserAsset;
use modules\users\Module;

$this->title = \Yii::t('app/modules/organization', 'Организации');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app/modules/organization', 'Организации'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;

UserAsset::register($this);

?>

<div class="users-backend-default-index">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($this->title) ?></h3>

            <div class="box-tools pull-right"></div>
        </div>
        <?php Pjax::begin([
            'id' => 'pjax-container',
            'enablePushState' => false,
            'timeout' => 5000,
        ]); ?>
        <div class="box-body">

            <?php if (Yii::$app->user->can(\modules\rbac\models\Role::ROLE_SUPER_ADMIN)) : ?>
                <div class="pull-right margin-left-large" style="margin-left: 10px;">
                    <p>
                        <?= Html::a('<span class="fa fa-plus"></span> ' . \Yii::t('app/modules/organization', 'Добавить'), ['create'], [
                            'class' => 'btn btn-block btn-success',
                            'title' => \Yii::t('app/modules/organization', 'Создать'),
                            'data' => [
                                'toggle' => 'tooltip',
                                'placement' => 'left',
                                'pjax' => 0,
                            ],
                        ]) ?>
                    </p>
                </div>
            <?php endif; ?>

            <?= GridView::widget([
                'id' => 'grid-users',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'filterSelector' => 'select[name="per-page"]',
                'layout' => "{items}",
                'tableOptions' => [
                    'class' => 'table table-bordered table-hover',
                ],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'name',
                        'label' => \Yii::t('app/modules/organization', 'Название'),
                        'format' => 'raw',
                        'headerOptions' => ['width' => '120'],
                        'value' => function ($data) {
                            return Html::a($data->name, Url::to(['/organization/default/index', 'OrganizationSearch' => ['parent_id' => $data->id]]));
                        },
                    ],

                    [
                        'attribute' => 'alias',
                        'format' => 'raw',
                        'value' => function ($data) {
                            if (\Yii::$app->user->identity->isSuperAdmin()) {
                                $url = 'http://' . Url::to($data->alias . '.' . strtolower($_SERVER['HTTP_HOST']));
                                return Html::a($url, $url, ['target'=>'_blank']);
                            } else {
                                return $data->alias;
                            }
                        },
                    ],

                    [
                        'attribute' => 'text',
                        'label' => \Yii::t('app/modules/organization', 'Текст'),
                    ],

                    [
                        'attribute' => 'status',
                        'label' => \Yii::t('app/modules/organization', 'Статус'),
                        'format' => 'raw',
                        'value' => function ($data) {
                            return Html::a($data->statusLabelName, Url::to(['set-status', 'id' => $data->id]), [
                                    'id' => $data->id,
                                    'class' => 'link-status',
                                    'title' => \Yii::t('app/modules/organization', 'Click to change the status'),
                                    'data' => [
                                        'toggle' => 'tooltip',
                                        'pjax' => 0,
                                        'id' => $data->id,
                                    ],
                                ]);
                        },
//                        'filter' => Html::activeDropDownList($searchModel, 'status', $searchModel->statusesArray, [
//                            'class' => 'form-control',
//                            'prompt' => \Yii::t('app/modules/organization', '- all -'),
//                            'data' => [
//                                'pjax' => true,
//                            ],
//                        ]),
                        'headerOptions' => [
                            'class' => 'text-center',
                        ],
                        'contentOptions' => [
                            'class' => 'title-column',
                            'style' => 'width:150px',
                        ],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'contentOptions' => [
                            'class' => 'action-column'
                        ],
                        'template' => '{view} {update} {delete} {interpretation}',
                        'buttons' => [
                            'update' => function ($url) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                    'title' => \Yii::t('app/modules/organization', 'Редактировать'),
                                    'data' => [
                                        'toggle' => 'tooltip',
                                        'pjax' => 0,
                                    ]
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                if (Yii::$app->user->identity->isSuperAdmin()) {
                                    $linkOptions = [
                                        'title' => \Yii::t('app/modules/organization', 'Удалить'),
                                        'data' => [
                                            'toggle' => 'tooltip',
                                            'method' => 'post',
                                            'pjax' => 0,
                                            'confirm' => \Yii::t('app/modules/organization', 'Вы действительно хотите удалить категорию "{:name}"?', [':name' => $model->name]),
                                        ]
                                    ];
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $linkOptions);
                                }
                            }
                        ]
                    ],
                ],
            ]); ?>
        </div>
        <div class="box-footer">
            <?= LinkPager::widget([
                'pagination' => $dataProvider->pagination,
                'registerLinkTags' => true,
                'options' => [
                    'class' => 'pagination pagination-sm no-margin pull-right',
                ]
            ]) ?>
        </div>
        <?php Pjax::end(); ?>
    </div>
</div>
