<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\helpers\Html;

$this->title = 'Есептер тізімі';
?>

<div class="reports-index">
    <div class="app-header sticky-header d-flex justify-content-between align-items-center p-4 mb-4 rounded">
        <div class="header-title">
            <h1 class="m-0"><?= Html::encode($this->title) ?></h1>
            <p class="subtitle">Сіздің есептеріңізді басқару</p>
        </div>
        <div class="header-actions">
            <?= Html::a('Қосу', ['create'], ['class' => 'btn btn-success btn-lg']) ?>
        </div>
    </div>

    <?php if ($dataProvider->getCount() > 0): ?>
        <div class="row">
            <?php foreach ($dataProvider->models as $report):
                $borderClass = ($report->status == 3) ? 'border-danger' : ''; ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="report-block shadow-lg p-4 rounded <?= $borderClass ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="report-title"><?= Html::encode($report->name) ?></h5>
                            <div class="action-buttons">
                                <?php if ($report->send_status ==0) :?>
                                    <?= Html::a('Жіберу', ['send', 'id' => $report->id, 'return'=>'index'], [
                                        'class' => 'btn btn-success btn-sm',
                                        'data' => [
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                                <?php endif; ?>
                                <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $report->id], ['class' => 'btn btn-link text-secondary', 'title' => 'Просмотр']) ?>
                                <?php if ($report->send_status ==0) :?>
                                <?= Html::a('<i class="fas fa-edit"></i>', ['update', 'id' => $report->id], ['class' => 'btn btn-link text-secondary', 'title' => 'Редактировать']) ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($report->files)): ?>
                            <div id="carousel-<?= $report->id ?>" class="carousel slide mb-3" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <?php foreach ($report->files as $index => $file): ?>
                                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                            <img src="<?= $file->url ?>" class="d-block w-100 thumbnail" alt="Image">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (count($report->files) > 1): ?>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?= $report->id ?>" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?= $report->id ?>" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>


                        <p><strong>Бағыт:</strong> <?= Html::encode($report->direction->name['kk']) ?></p>
                        <?php if ($report->status == 3): ?>
                            <p class="text-danger"><strong>Орындалмады</strong></p>
                        <?php else: ?>
                            <p><strong><?= Html::encode($report->dateRangeLabel) ?>:</strong> <?= Html::encode($report->dateRange) ?></p>
                        <?php endif; ?>
                        <p><strong><?= Html::encode($report->perLabel) ?>:</strong> <?= Html::encode($report->perValue) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Пагинация -->
        <div class="pagination-wrapper d-flex justify-content-center mt-4">
            <?php
            // Логика пагинации
            $totalPages = $dataProvider->pagination->pageCount;
            $currentPage = $dataProvider->pagination->page + 1;

            for ($i = 1; $i <= $totalPages; $i++) {
                if ($i == 1 || $i == $totalPages || ($i >= $currentPage - 2 && $i <= $currentPage + 2)) {
                    echo Html::a($i, ['index', 'page' => $i - 1], [
                        'class' => 'page-link' . ($i == $currentPage ? ' active' : ''),
                    ]);
                } elseif ($i == $currentPage - 3 || $i == $currentPage + 3) {
                    echo '<span>...</span>';
                }
            }
            ?>
        </div>

    <?php else: ?>
        <p>Қазіргі уақытта есептер жоқ.</p>
    <?php endif; ?>
</div>

<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f8f9fa;
        color: #333;
    }

    .app-header {
        background: linear-gradient(90deg, rgba(0, 123, 255, 0.7), rgba(0, 86, 179, 0.86));
        color: white;
        border-radius: 8px;
    }

    .sticky-header {
        position: sticky;
        top: 0; /* Прикрепить к верхней части окна */
        z-index: 1000; /* Убедитесь, что заголовок выше других элементов */
    }

    .header-title h1 {
        margin-bottom: 0.5rem;
        font-size: 2rem; /* Увеличенный размер заголовка */
    }

    .header-title .subtitle {
        font-size: 1rem;
        margin-top: 0.2rem;
    }

    .report-block {
        border: 1px solid #ddd;
        transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
        position: relative;
        background-color: #fff;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .report-block:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
    }

    .border-danger {
        border-color: red !important; /* Красная рамка */
    }

    .report-title {
        font-size: 1.5rem; /* Увеличенный размер заголовка блока */
        margin-bottom: 10px;
        color: #343a40; /* Темный цвет заголовка */
    }

    .action-buttons {
        display: flex;
        gap: 10px; /* Промежуток между кнопками */
    }

    .btn-link {
        font-size: 1.5rem; /* Увеличенный размер иконок */
        padding: 0; /* Убираем отступы */
    }

    .btn-link:hover {
        color: #0056b3; /* Цвет при наведении */
    }

    .text-danger {
        font-weight: bold;
        font-size: 1.2rem; /* Увеличенный размер текста для статуса "Орындалмады" */
    }

    .pagination-wrapper {
        margin-top: 20px;
    }

    .page-link {
        padding: 8px 16px;
        margin-right: 5px;
        border-radius: 4px;
        border: none;
        background-color: lightgray; /* Светло-серый цвет фона */
        color: #333; /* Цвет текста */
        text-decoration: none;
    }

    .page-link:hover {
        background-color: #ccc; /* Цвет при наведении */
    }

    .active.page-link {
        background-color: #007bff;
        color: white;
    }

    .carousel {
        background: #f8f9fa;
        border-radius: 4px;
        overflow: hidden;
    }

    .carousel .thumbnail {
        height: 200px;
        object-fit: cover;
        border-radius: 4px;
    }

    .carousel-control-prev,
    .carousel-control-next {
        width: 30px;
        background: rgba(0,0,0,0.2);
    }

    .carousel-control-prev:hover,
    .carousel-control-next:hover {
        background: rgba(0,0,0,0.4);
    }

</style>

<!-- Подключите Font Awesome для иконок -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
