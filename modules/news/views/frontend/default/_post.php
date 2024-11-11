<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;


$news = [
    [
        'title' => "День работодателя проводится впервые в казахстанских колледжах",
        'views' => 75,
        'date' => "20 января 2023",
        'img'=> "https://tengrinews.kz/userdata/news/2022/news_458310/thumb_m/photo_384867.jpeg"
    ],
    [
        'title' => "Краткий дайджест главных новостей Министерства просвещения за минувшую неделю",
        'views' => 75,
        'date' => "20 января 2023",
        'img'=> "https://tengrinews.kz/userdata/news/2022/news_458310/thumb_m/photo_384867.jpeg"
    ],
    [
        'title' => "Министр просвещения Гани Бейсембаев провел совещание с руководителями управлений образования всех регионов...",
        'views' => 75,
        'date' => "20 января 2023",
        'img'=> "https://tengrinews.kz/userdata/news/2022/news_458310/thumb_m/photo_384867.jpeg"
    ],
    [
        'title' => "Асхат Аймагамбетов о нововведениях в работе колледжей и предоставлении им академической свободы",
        'views' => 75,
        'date' => "20 января 2023",
        'img'=> "https://tengrinews.kz/userdata/news/2022/news_458310/thumb_m/photo_384867.jpeg"
    ],
    [
        'title' => "Асхат Аймагамбетов о нововведениях в работе колледжей и предоставлении им академической свободы",
        'views' => 75,
        'date' => "20 января 2023",
        'img'=> "https://tengrinews.kz/userdata/news/2022/news_458310/thumb_m/photo_384867.jpeg"
    ],
    [
        'title' => "Асхат Аймагамбетов о нововведениях в работе колледжей и предоставлении им академической свободы",
        'views' => 75,
        'date' => "20 января 2023",
        'img'=> "https://tengrinews.kz/userdata/news/2022/news_458310/thumb_m/photo_384867.jpeg"
    ]
];

?>


<!-- Верстка для news/view -->

<div class="d-flex justify-content-between">
    <div class="newsViewPageMain">
        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
            <i class="fa fa-angle-left mr-2" aria-hidden="true"></i>
            <?=Yii::t('app', 'Назад') ?>
        </a>
        <span class="newsViewDate"><?= Yii::$app->formatter->format($model->created_at, 'datetime'); ?></span>
        <span class="newsViewTitle"><?= Html::encode($model->title); ?></span>
        <div class="newsViewLinkBody">
            <span class="newsViewLink">Ссылка на источник:</span>
            <a href="">https://m.facebook.com/story.php?story_fbid=3531096973605663&id=100001161867984</a>
        </div>
        <div class="d-flex flex-column mb-3">
            <?= HtmlPurifier::process($model->content); ?>
        </div>
        <div>
            <i class="fa fa-eye mr-2" aria-hidden="true"></i>
            <span><?php /* $item['views']*/ ?></span>
        </div>
    </div>
    <div class="newsViewPageSecond">
        <div class="allNewsBody">
            <div class="newsViewListPage">
                <i class="fa fa-news" aria-hidden="true"></i>
                <span class="allNews"><?=Yii::t('app', 'Все новости') ?></span>
            </div>
            <div class="newsViewPageSecondBody">
                <?php foreach ($news as $item) { ?>
                    <div class="newsViewByOne">
                        <span class="newsViewDate"><?=$item['date']?></span>
                        <span><?=$item['title']?></span>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>


<div class="post">
    <h2></h2>

    <?php
    if ($model->image) {
        echo '<div class="col-xs-12 col-sm-12">' . Html::img($model->getImagePath(true) . '/' . $model->image, ['class' => 'img-responsive']) . '</div>';
    }
    ?>



</div>
