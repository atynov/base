<?php

use PhpParser\Node\Expr\AssignOp\Div;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model modules\news\models\News */
/*
if (!empty($model->title))
    $this->title = $model->title;
else
    $this->title = $model->name;

if (!empty($model->description))
    $this->registerMetaTag(['content' => Html::encode($model->description), 'name' => 'description']);

if (!empty($model->keywords))
    $this->registerMetaTag(['content' => Html::encode($model->keywords), 'name' => 'keywords']);


if (isset($model->route))
    $this->registerLinkTag(['rel' => 'canonical', 'href' => Url::to($model->route.'/'.$model->alias, true)]);
elseif (isset($route))
    $this->registerLinkTag(['rel' => 'canonical', 'href' => Url::to($route.'/'.$model->alias, true)]);
else
    $this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);
*/
?>
<div>
    <h3 class="newsBlogPage"><?=Yii::t('app', 'Новости') ?></h3>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_list',
        'itemOptions' => [
            'class' => 'newsCart mr-0 mt-4'
        ],
        'options' => [
            'class' => 'newsPageBody'
        ],
        'layout' => "{items}"
    ]);
    ?>
    <nav class="paginationNewsPage" aria-label="...">
        <?= \yii\widgets\LinkPager::widget([
            'pagination' => $dataProvider->pagination,
            'linkContainerOptions' => [
                'class' => 'page-item paginationBtnNewsPage',
            ],
            'linkOptions' => [
                'class' => 'page-link page-link-newsPage',
            ],
            'nextPageLabel' => '<i class="fa fa-angle-right" aria-hidden="true"></i>',
            'prevPageLabel' => '<i class="fa fa-angle-left" aria-hidden="true"></i>'
        ]); ?>
    </nav>
</div>
