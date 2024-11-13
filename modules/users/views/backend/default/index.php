<?php

/**
 * @var $this yii\web\View
 * @var $searchModel modules\users\models\search\UserSearch
 * @var $model modules\users\models\User
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $assignModel \modules\rbac\models\Assignment
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\web\View;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use yii\web\JsExpression;
use backend\assets\plugins\DatePickerAsset;
use modules\users\assets\UserAsset;
use modules\users\Module;

$this->title = Module::t('module', 'Қолданушылар');
$this->params['breadcrumbs'][] = $this->title;

UserAsset::register($this);

$language = substr(Yii::$app->language, 0, 2);
DatePickerAsset::$language = $language;
DatePickerAsset::register($this);

$js = new JsExpression("
    initDatePicker();
    $(document).on('ready pjax:success', function() {
       initDatePicker();
    });

    function initDatePicker()
    {
        /** @see http://bootstrap-datepicker.readthedocs.io/en/latest/index.html */
        $('#datepicker').datepicker({
            language: '{$language}',
            autoclose: true,
            format: 'dd.mm.yyyy',
            zIndexOffset: 1001,
            orientation: 'bottom',
            todayHighlight: true
        });
    }
");
$this->registerJs($js, View::POS_END);

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
            <div class="pull-left">
                <?= common\widgets\PageSize::widget([
                    'label' => '',
                    'defaultPageSize' => 25,
                    'sizes' => [10 => 10, 15 => 15, 20 => 20, 25 => 25, 50 => 50, 100 => 100, 200 => 200],
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]) ?>
            </div>
            <div class="pull-right">
                <p>
                    <?= Html::a('<span class="fa fa-plus"></span> ', ['create'], [
                        'class' => 'btn btn-block btn-success',
                        'title' => Yii::t('app', 'Қосу'),
                        'data' => [
                            'toggle' => 'tooltip',
                            'placement' => 'left',
                            'pjax' => 0,
                        ],
                    ]) ?>
                </p>
            </div>
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
                        'attribute' => 'username',
                        'filter' => Html::activeInput('text', $searchModel, 'username', [
                            'class' => 'form-control',
                            'placeholder' => Module::t('module', '- мәтін -'),
                            'data' => [
                                'pjax' => true,
                            ],
                        ]),
                        'label' => Module::t('module', 'Қолданушылар'),
                        'format' => 'raw',
                        'value' => function ($data) {
                            $view = Yii::$app->controller->view;
                            return $view->render('_avatar_column', ['model' => $data]);
                        },
                        'headerOptions' => ['width' => '120'],
                    ],
                    [
                        'attribute' => 'email',
                        'filter' => Html::activeInput('text', $searchModel, 'email', [
                            'class' => 'form-control',
                            'placeholder' => Module::t('module', '- мәтін -'),
                            'data' => [
                                'pjax' => true,
                            ],
                        ]),
                        'format' => 'email'
                    ],
                    [
                        'attribute' => 'status',
                        'filter' => Html::activeDropDownList($searchModel, 'status', $searchModel->statusesArray, [
                            'class' => 'form-control',
                            'prompt' => Module::t('module', '- бәрі -'),
                            'data' => [
                                'pjax' => true,
                            ],
                        ]),
                        'format' => 'raw',
                        'value' => function ($data) {
                            /** @var object $identity */
                            $identity = Yii::$app->user->identity;
                            if ($data->id !== $identity->id && !$data->isSuperAdmin($data->id)) {
                                return Html::a($data->statusLabelName, Url::to(['set-status', 'id' => $data->id]), [
                                        'id' => $data->id,
                                        'class' => 'link-status',
                                        'title' => Module::t('module', 'Мәртебені өзгерту үшін басыңыз'),
                                        'data' => [
                                            'toggle' => 'tooltip',
                                            'pjax' => 0,
                                            'id' => $data->id,
                                        ],
                                    ]) . ' ' .
                                    Html::a($data->labelMailConfirm, Url::to(['send-confirm-email', 'id' => $data->id]), [
                                        'id' => 'email-link-' . $data->id,
                                        'class' => 'link-email',
                                        'title' => Module::t('module', 'Тіркеу сілтемесін жіберу'),
                                        'data' => [
                                            'toggle' => 'tooltip',
                                        ],
                                    ]);
                            }
                            return $data->statusLabelName;
                        },
                        'headerOptions' => [
                            'class' => 'text-center',
                        ],
                        'contentOptions' => [
                            'class' => 'title-column',
                            'style' => 'width:150px',
                        ],
                    ],
                    [
                        'attribute' => 'userRoleName',
                        'filter' => Html::activeDropDownList($searchModel, 'userRoleName', $assignModel->getRolesArray(), [
                            'class' => 'form-control',
                            'prompt' => Module::t('module', '- бәрі -'),
                            'data' => [
                                'pjax' => true,
                            ],
                        ]),
                        'format' => 'raw',
                        'value' => function ($data) use ($assignModel) {
                            return $assignModel->getUserRoleName($data->id);
                        },
                        'contentOptions' => [
                            'style' => 'width:200px',
                        ],
                    ],
                    [
                        'attribute' => 'organization_id',
                        'label' => Module::t('module', 'Ұйым'),
                        'value' => function ($model) {
                            return $model->organization ? $model->organization->name['kk'] : null;
                        },
                        'filter' => Html::activeDropDownList($searchModel, 'organization_id',
                            \modules\organization\models\Organization::find()->select(['name', 'id'])->indexBy('id')->column(),
                            ['class' => 'form-control', 'prompt' => Module::t('module', '- барлығы -')]
                        ),
                        'headerOptions' => ['width' => '200'],
                    ],
                    [
                        'attribute' => 'profile.last_visit',
                        'filter' => '<div class="form-group"><div class="input-group date"><div class="input-group-addon"><i class="fa fa-calendar"></i></div>'
                            . Html::activeInput('text', $searchModel, 'date_from', [
                                'id' => 'datepicker',
                                'class' => 'form-control',
                                'placeholder' => Module::t('module', '- таңдау -'),
                                'data' => [
                                    'pjax' => true,
                                ],
                            ]) . '</div></div>',
                        'format' => 'datetime',
                        'headerOptions' => [
                            'style' => 'width: 165px;'
                        ]
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'contentOptions' => [
                            'class' => 'action-column'
                        ],
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                            'view' => function ($url) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                    'title' => Module::t('module', 'Көру'),
                                    'data' => [
                                        'toggle' => 'tooltip',
                                        'pjax' => 0,
                                    ]
                                ]);
                            },
                            'update' => function ($url) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                    'title' => Module::t('module', 'Өңдеу'),
                                    'data' => [
                                        'toggle' => 'tooltip',
                                        'pjax' => 0,
                                    ]
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                $linkOptions = [
                                    'title' => Module::t('module', 'Жою'),
                                    'data' => [
                                        'toggle' => 'tooltip',
                                        'method' => 'post',
                                        'pjax' => 0,
                                        'confirm' => Module::t('module', 'Қолданушы "{:name}" жойылады!', [':name' => $model->username]),
                                    ]
                                ];
                                if ($model->isDeleted()) {
                                    $linkOptions = [
                                        'title' => Module::t('module', 'Жою'),
                                        'data' => [
                                            'toggle' => 'tooltip',
                                            'method' => 'post',
                                            'pjax' => 0,
                                            'confirm' => Module::t('module', 'Қалпына келтіру мүмкін емес!'),
                                        ],
                                    ];
                                }
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $linkOptions);
                            },
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
