<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->registerJsFile('@web/js/embed.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="report-view">
    <div class="app-header d-flex justify-content-between align-items-center p-4 mb-4 rounded">
        <div class="header-title">
            <h1 class="m-0"><?= Html::encode($model->name) ?></h1>
        </div>
        <div class="header-actions">
            <?php if ($model->send_status ==0) :?>
                <?= Html::a('Жіберу', ['send', 'id' => $model->id, 'return'=>'view'], [
                    'class' => 'btn btn-success',
                    'data' => [
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
            <?= Html::a('<i class="fas fa-arrow-left"></i> Артқа', ['index'], ['class' => 'btn btn-secondary']) ?>
            <?= Html::a('<i class="fas fa-edit"></i> Өңдеу', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </div>

    </div>
    <div class="col-md-6">
        <p><strong>Бағыт:</strong> <?= Html::encode($model->direction->name['kk']) ?></p>
        <p>
            <strong>Статус:</strong>
            <?php
            $statusClass = $model->status == 3 ? 'text-danger' : ($model->status == 2 ? 'text-success' : 'text-warning');
            echo '<span class="' . $statusClass . '">' . Html::encode($model->statusLabel) . '</span>';
            ?>
        </p>
        <p><strong><?= Html::encode($model->perLabel) ?>:</strong> <?= Html::encode($model->perValue) ?></p>
        <p><strong><?= Html::encode($model->dateRangeLabel) ?>:</strong> <?= Html::encode($model->dateRange) ?></p>
        <?php if ($model->people_count): ?>
            <p><strong>Қамтылған адам саны:</strong> <?= Html::encode($model->people_count) ?></p>
        <?php endif; ?>
        <?php if ($model->link): ?>
            <p><strong>Сілтеме:</strong> <?= Html::a($model->link, $model->link, ['target' => '_blank']) ?></p>
        <?php endif; ?>
        <?php if ($model->description): ?>
            <p><strong>Сипаттама:</strong> <?= Html::encode($model->description) ?></p>
        <?php endif; ?>
    </div>

    <?php if (!empty($files)): ?>
        <div class="col-md-6">
            <div class="images-gallery">
                <strong>Суреттер:</strong>
                <div class="row">
                    <?php foreach ($files as $index => $file): ?>
                        <div class="col-md-4 mb-3">
                            <img src="<?= $file->url ?>"
                                 class="img-thumbnail gallery-image"
                                 data-bs-toggle="modal"
                                 data-bs-target="#imageModal"
                                 data-index="<?= $index ?>"
                                 alt="Image">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($model->link): ?>
        <div class="embed-preview mt-3">
            <?php
            if (strpos($model->link, 'youtube.com') !== false || strpos($model->link, 'youtu.be') !== false) {
                // Получаем ID видео из ссылки
                preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|shorts)\/))([^\?&\"'>]+)/", $model->link, $matches);
                if (!empty($matches[1])) {
                    $videoId = $matches[1];
                    echo '<div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/' . $videoId . '" 
                                    allowfullscreen></iframe>
                          </div>';
                }
            } elseif (strpos($model->link, 'instagram.com') !== false) {
                echo '<div class="instagram-embed">
                        <blockquote class="instagram-media" data-instgrm-permalink="' . $model->link . '">
                        </blockquote>
                        <script async src="/uploads/embed.js"></script>
                      </div>';
            }
            ?>
        </div>
    <?php endif; ?>
</div>

<!-- Модальное окно для просмотра изображений -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body p-0">
                <button type="button" class="btn-close position-absolute end-0 p-3" data-bs-dismiss="modal"></button>
                <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($files as $index => $file): ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <img src="<?= $file->url ?>" class="d-block w-100" alt="Image">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($files) > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .gallery-image {
        width: 100%;
        height: 150px;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.3s;
    }

    .gallery-image:hover {
        transform: scale(1.05);
    }

    .modal-body {
        padding: 0;
    }

    .carousel-item img {
        max-height: 80vh;
        object-fit: contain;
    }

    .instagram-embed {
        max-width: 540px;
        margin: 0 auto;
        min-height: 400px;
    }

    .embed-preview {
        max-width: 800px;
        margin: 0 auto;
        min-height: 400px;
    }

</style>

<?php
$this->registerJs(<<<JS
    // Открытие модального окна с нужным слайдом
    $('.gallery-image').on('click', function() {
        let index = $(this).data('index');
        $('#imageCarousel').carousel(index);
    });
JS
);


?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
