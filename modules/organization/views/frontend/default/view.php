<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use modules\organization\Module;
use yii\helpers\Url;
use yii\grid\GridView;
use kartik\editable\Editable;

use kartik\popover\PopoverX;



/* @var $this yii\web\View */

$this->title = \Yii::t('app/modules/organization', 'Организации');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app/modules/organization', 'Организации'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ['label' => $model->category->name, 'url' => ['index']];
$this->params['breadcrumbs'][] = Html::encode($model->name);
?>

<div class="organization-assign-view">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($model->name) ?>
                <small></small>
            </h3>
        </div>
        <div class="box-body">
            <div class="pull-left"></div>
            <div class="pull-right"></div>



            <div class="row">
                <div class="col-md-6">
                    <?= $model->description ?>


                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'attribute' => 'required_amount',
                                'value' => function ($data) {
                                    return $data->required_amount . (!empty($data->required_amount_unit) ? ' ' . $data->getAmountUnit() : '');
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'delivery_address',
                                'format' => 'raw',
                                'value' => function ($data) {
                                    return $data->city->name . ' / ' . $data->delivery_address;
                                },
                            ],

                            [
                                'attribute' => 'delivery_date',
                                'format' => 'raw',
                                'value' => function ($data) {
                                    return $data->delivery_date;
                                },
                            ],
                            [
                                'attribute' => 'end_date',
                                'format' => 'raw',
                                'value' => function ($data) {
                                    $dt = new DateTime("now", new DateTimeZone("Asia/Almaty"));
                                    return $data->end_date . (date('Y-m-d', strtotime($data->end_date)) == date('Y-m-d') && date('H:i:s', strtotime($data->end_date)) > $dt->format('H:i:s') ? '<div>'.\Yii::t('app/modules/organization', 'Осталось до завершения').': <span id="time" style="color: red;" ></span> </div>' : '');
                                },
                            ],

/*
                            [
                                'attribute' => 'is_joint purchase',
                                'format' => 'raw',
                            ],



                            [
                                'attribute' => 'purchase_type',
                                'label' => \Yii::t('app/modules/organization', 'Частота закупки'),
                                'format' => 'raw',
                                'value' => function ($data) {
                                    return $data->getPurchaseType();
                                },
                            ],

                            [
                                'attribute' => 'supplier_requirement',
                                'format' => 'raw',
                                'value' => function ($data) {
                                    return $data->getSupplierRequirement();
                                },
                            ],
*/
                            [
                                'attribute' => 'organization_id',
                                'label' => \Yii::t('app/modules/organization', 'Компания'),
                                'format' => 'raw',
                                'value' => function ($data) {
                                    return Html::a(
                                        $data->organization->name,
                                        [Url::base() . '/organization/default/view/', 'id' => $data->organization_id],
                                        [
                                            'target' => '_blank'
                                        ]
                                    );
                                },
                            ],

                            [
                                'attribute' => 'publish_date',
                                'format' => 'raw',
                            ],
                        ],
                    ]) ?>
                </div>

                <?php if (!empty($dpFiles) && $model->fileToContents) : ?>
                <div class="col-md-6">
                        <legend><?= \Yii::t('app/modules/organization', 'Файлы') ?></legend>
                    <?= GridView::widget([
                        'id' => 'grid-files',
                        'dataProvider' => $dpFiles,
                        'layout' => "{items}",
                        'tableOptions' => [
                            'class' => 'table table-bordered table-hover',
                        ],
                        'columns' => [
                            [
                                'attribute' => 'filename',
                                'label' => \Yii::t('app/modules/organization', 'Название'),
                                'format' => 'raw',
                                'value' => function ($data) {
                                    return Html::a(
                                        $data->file->filename,
                                        Url::home() . 'uploads/' . $data->file->filename,
                                        [
                                            'target' => '_blank'
                                        ]
                                    );
                                },
                                'headerOptions' => [
                                    'class' => 'text-center',
                                ],
                                'organizationOptions' => [
                                    'class' => 'title-column',
                                    'style' => 'width:150px',
                                ],
                            ],

                            [
                                'attribute' => 'size',
                                'label' => \Yii::t('app/modules/organization', 'Размер'),
                                'format' => 'raw',
                                'value' => function ($data) {
                                    return \Yii::$app->formatter->asShortSize( $data->file->size);
                                },
                                'headerOptions' => [
                                    'class' => 'text-center',
                                ],
                            ],

                            [
                                'attribute' => 'create_date',
                                'label' => \Yii::t('app/modules/organization', 'Дата добавления'),
                                'format' => 'raw',
                                'value' => function ($data) {
                                    return $data->file->create_date;
                                },
                                'headerOptions' => [
                                    'class' => 'text-center',
                                ],
                            ],
                        ],
                    ]);
                    ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($dpAucs) && !empty($dpAucs->totalCount)) : ?>
            <div class="col-md-12">
                <legend><?= \Yii::t('app/modules/organization', 'Партнеры') ?></legend>
                <?= GridView::widget([
                    'id' => 'grid-aucs',
                    'dataProvider' => $dpAucs,
                    'layout' => "{items}",
                    'tableOptions' => [
                        'class' => 'table table-bordered table-hover',
                    ],
                    'columns' => [
                        [
                            'attribute' => 'required_amount',
                            'value' => function ($data) {
                                return $data->required_amount . (!empty($data->required_amount_unit) ? ' ' . $data->getAmountUnit() : '');
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'delivery_address',
                            'label' => \Yii::t('app/modules/organization', 'Адрес'),
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'organization_id',
                            'label' => \Yii::t('app/modules/organization', 'Компания'),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return Html::a(
                                    $data->organization->name,
                                    [Url::base() . '/organization/default/view', 'id' => $data->organization_id],
                                    [
                                        'target' => '_blank'
                                    ]
                                );
                            },
                            'headerOptions' => [
                                'class' => 'text-center',
                            ],
                            'organizationOptions' => [
                                'class' => 'title-column',
                                'style' => 'width:150px',
                            ],
                        ],

                        [
                            'attribute' => 'create_date',
                            'label' => \Yii::t('app/modules/organization', 'Дата добавления'),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return $data->create_date;
                            },
                            'headerOptions' => [
                                'class' => 'text-center',
                            ],
                        ],
                    ],
                ]);
                ?>
            </div>
        <?php endif; ?>


        <?php if (!empty($dpBids)) : ?>
            <div class="col-md-12">
                <legend><?= \Yii::t('app/modules/organization', 'Ставки') ?></legend>
                <?php \yii\widgets\Pjax::begin(); ?>
                <?= GridView::widget([
                    'id' => 'grid-bids',
                    'dataProvider' => $dpBids,
                    'layout' => "{items}",
                    'tableOptions' => [
                        'class' => 'table table-bordered table-hover',
                    ],
                    'columns' => [
                        [
                            'attribute' => 'price_retail',
                            'label' => \Yii::t('app/modules/organization', 'Цена'),
                            'format' => 'raw',
                            'value' => function ($data) {
                                $price_history = '';
                                if ($data->organizationBidHistories) {
                                    $organization = '<table width="100%" border="0"><th>Цена</th><th> </th><th>Дата</th>';
                                    foreach ($data->organizationBidHistories as $row) {
                                        $organization .= '<tr><td>'.$row->value_old.'</td><td> </td><td>'.$row->create_date.'</td></tr>';
                                    }
                                    $organization .= '</table>';

                                    $price_history = ' ' . PopoverX::widget([
                                            'header' => \Yii::t('app/modules/organization', 'История изменения цены'),
                                            'size' => PopoverX::SIZE_MEDIUM,
                                            'placement' => PopoverX::ALIGN_RIGHT,
                                            'organization' => $organization,
                                            'toggleButton' => ['tag'=>'span', 'label'=>'', 'class'=>'glyphicon glyphicon-equalizer', 'aria-hidden'=>"true"],
                                        ]);
                                }

                    if ($data->canUpdate()) {
                        $result = Editable::widget([
                            'name'=>'ContentBid[price_retail]',
                            'asPopover' => false,
                            'value' => $data->price_retail,
                            'header' => \Yii::t('app/modules/organization', 'Цена'),
                            'size'=>'md',
                            'pjaxContainerId' => 'grid-bids',
                            'options' => [
                                'class'=>'form-control',
                                'placeholder'=>\Yii::t('app/modules/organization', 'Введите новую цену'),
                            ],
                            'formOptions' => [
                                'action' => ['bid/update?id=' . $data->id],
                            ],
                            'pluginEvents' => [
                                "editableSuccess"=>"function(event, val, form, data) { $('#grid-bids').yiiGridView('applyFilter'); }",
                            ]
                        ]);
                    } else {
                        $result = $data->price_retail;
                    }

                    return $result . $price_history;

                            },
                        ],
                        [
                            'attribute' => 'price_retail_comment',
                            'label' => \Yii::t('app/modules/organization', 'Комментарий'),
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'organization_id',
                            'label' => \Yii::t('app/modules/organization', 'Компания'),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return Html::a(
                                    $data->organization->name,
                                    [Url::base() . '/organization/default/view', 'id' => $data->organization_id],
                                    [
                                        'target' => '_blank'
                                    ]
                                );
                            },
                            'headerOptions' => [
                                'class' => 'text-center',
                            ],
                            'organizationOptions' => [
                                'class' => 'title-column',
                                'style' => 'width:150px',
                            ],
                        ],

                        [
                            'attribute' => 'create_date',
                            'label' => \Yii::t('app/modules/organization', 'Дата добавления'),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return $data->create_date;
                            },
                            'headerOptions' => [
                                'class' => 'text-center',
                            ],
                        ],

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'organizationOptions' => [
                                'class' => 'action-column'
                            ],
                            'template' => '{view}',
                            'buttons' => [
                                'view' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['bid/view', 'id' => $model->id], [
                                        'title' => \Yii::t('app/modules/organization', 'Просмотр'),
                                        'data' => [
                                            'toggle' => 'tooltip',
                                            'pjax' => 0,
                                        ],
                                        'target' => '_blank'
                                    ]);
                                }
                            ]
                        ],
                    ],
                ]);
                ?>
                <?php \yii\widgets\Pjax::end(); ?>
            </div>
        <?php endif; ?>


        <div class="box-footer">

            <?php

            $canBidOfferAction = true;
            // проверяем если юзер подавал уже ставку
            if ($model->organizationBids) {
                foreach ($model->organizationBids as $row) {
                    if ($row->create_user_id == Yii::$app->user->id) {
                        $canBidOfferAction = false;
                        $myBidCreateDate = $row->create_date;
                        break;
                    }
                }
            }

            // проверяем если юзер является автором
            if ($model->create_user_id == Yii::$app->user->id) {
                $canBidOfferAction = false;
            }

            // проверяем если юзер подавал предложение о совместном закупе
            if ($model->organizations) {
                foreach ($model->organizations as $row) {
                    if ($row->create_user_id == Yii::$app->user->id) {
                        $canBidOfferAction = false;
                        $myOfferCreateDate = $row->create_date;
                        break;
                    }
                }
            }
            ?>

            <div class="pull-left">
                <?php if ($canBidOfferAction) : ?>
                    <?= Html::a('<span class="glyphicon glyphicon-" aria-hidden="true"></span> ' . \Yii::t('app/modules/organization', 'Подать ставку на этот аукцион'), Url::to(['bid/create', 'id' => $model->id]), [
                        'class' => 'btn btn-success',
                        'data' => [
                            'confirm' => \Yii::t('app/modules/organization', 'Вы действительно хотите подать ставку?'),
                        ],
                    ]) ?>
                <?php elseif (!empty($myBidCreateDate)): ?>
                    <?= Html::tag('p', \Yii::t('app/modules/organization', 'Вы подавали ставку на этот аукцион') . ' ' . $myBidCreateDate)  ?>
                <?php endif; ?>
            </div>

            <div class="pull-right">
                <?php if ($canBidOfferAction) : ?>
                    <?= Html::a('<span class="glyphicon glyphicon-" aria-hidden="true"></span> ' . \Yii::t('app/modules/organization', 'Подать предложение о совместной покупки'), Url::to(['create', 'parent_id' => $model->id]), [
                        'class' => 'btn btn-primary',
                        'data' => [
                            'method' => 'post',
                        ],
                    ]) ?>
                <?php elseif (!empty($myOfferCreateDate)): ?>
                    <?= Html::tag('p', \Yii::t('app/modules/organization', 'Вы подавали предложение о совместной покупке') . ' ' . $myOfferCreateDate)  ?>
                <?php endif; ?>


            </div>

            <?php /* if ($model->create_user_id == Yii::$app->user->id) : ?>
            <div class="pull-right">
                <?= Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> ' . \Yii::t('app/modules/organization', 'Редактировать'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span> ' . \Yii::t('app/modules/organization', 'Удалить'), ['revoke', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => \Yii::t('app/modules/organization', 'Вы действительно хотите удалить запись?'),
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
            <?php endif;*/ ?>
        </div>
    </div>
</div>

<?php
if (date('Y-m-d', strtotime($model->end_date)) == date('Y-m-d') ) {
    $this->registerJs("
function startTimer(end_date, display) {
    var start = Date.now(),
        diff,
        minutes,
        seconds;
    function timer() {
        var t = Date.parse(new Date(end_date)) - Date.parse(new Date());
        if (t > 0) {
            var seconds = Math.floor( (t/1000) % 60 );
            var minutes = Math.floor( (t/1000/60) % 60 );
            var hours = Math.floor( (t/(1000*60*60)) % 24 );
            
            minutes = minutes < 10 ? \"0\" + minutes : minutes;
            seconds = seconds < 10 ? \"0\" + seconds : seconds;
      
            if (hours>0) {
                display.textContent = hours + \":\" + minutes + \":\" + seconds;
            } else {
                display.textContent = minutes + \":\" + seconds;
            }
             
            if (diff <= 0) {
                start = Date.now() + 1000;
            }
        }        
    };
    
    timer();
    setInterval(timer, 1000);
}

window.onload = function () {
    var end_date = '".$model->end_date."',
        display = document.querySelector('#time');
    startTimer(end_date, display);
};
        ", yii\web\View::POS_END);
}






