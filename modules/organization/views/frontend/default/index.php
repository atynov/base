<?php

/**
 * @var $this yii\web\View
 * @var $searchModel modules\users\models\search\UserSearch
 * @var $model modules\users\models\User
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $assignModel \modules\rbac\models\Assignment
 */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use modules\users\assets\UserAsset;
use modules\organization\Module;


$this->title = Yii::t('app', 'Организации');
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Организации'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = $this->title;

UserAsset::register($this);

$this->params['wide_title'] = $this->title;

?>


<div class="users-backend-default-index" style="display: none">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($this->title) ?></h3>

            <div class="box-tools pull-right">
                <p>
                    <?= Html::a('<span class="fa fa-plus"></span> ' . \Yii::t('app/modules/organization', 'Создать новый'), ['create'], [
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
        </div>
        <?php Pjax::begin([
            'id' => 'pjax-container',
            'enablePushState' => false,
            'timeout' => 5000,
        ]); ?>
        <div class="box-body">



            <?= $this->render('_form_search', [
                'model' => $searchModel
            ]) ?>

            <?php Pjax::begin(['id' => 'records']) ?>
            <?= GridView::widget([
                'id' => 'grid-users',
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                //'filterSelector' => 'select[name="per-page"]',
                'layout' => "{items}",
                'tableOptions' => [
                    'class' => 'table table-bordered table-hover',
                ],
                'columns' => [
                    [
                        'attribute' => 'name',
                        'label' => \Yii::t('app/modules/organization', 'Заголовок'),
                        'format' => 'raw',
                        'headerOptions' => ['width' => '120'],
                        'value' => function ($data) {
                            return Html::a($data->name, ['view', 'id'=>$data->id]);
                        },
//                        'filter' => Html::activeInput('text', $searchModel, 'keyword', [
//                            'class' => 'form-control',
//                            // 'placeholder' => \Yii::t('app/modules/organization', ''),
//                            'data' => [
//                                'pjax' => true,
//                            ],
//                        ]),
                    ],
                    /*
                    [
                        'attribute' => 'description_ru',
                        'format' => 'raw',
                        'label' => \Yii::t('app/modules/organization', 'Описание'),
//                        'filter' => Html::activeInput('text', $searchModel, 'keyword', [
//                            'class' => 'form-control',
//                            // 'placeholder' => \Yii::t('app/modules/organization', ''),
//                            'data' => [
//                                'pjax' => true,
//                            ],
//                        ]),
                        'value' => function ($data) {
                            return Html::a($data->description_ru, ['view', 'id'=>$data->id]);
                        },
                    ],*/

                    /*
                    [
                        'attribute' => 'status',
                        'label' => \Yii::t('app/modules/organization', 'Статус'),
                        'format' => 'raw',
                        'value' => function ($data) {
                            return $data->statusLabelName;
                        },
                        'headerOptions' => [
                            'class' => 'text-center',
                        ],
                        'organizationOptions' => [
                            'class' => 'title-column',
                            'style' => 'width:150px',
                        ],
                    ],*/
                ],
            ]); ?>
            <?php Pjax::end() ?>
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

        <div class="pull-right">
            <?= common\widgets\PageSize::widget([
                'label' => '',
                'defaultPageSize' => 25,
                'sizes' => [10 => 10, 15 => 15, 20 => 20, 25 => 25, 50 => 50, 100 => 100, 200 => 200],
                'options' => [
                    'class' => 'form-control'
                ]
            ]); ?>
        </div>
        <?php Pjax::end(); ?>
    </div>
</div>
