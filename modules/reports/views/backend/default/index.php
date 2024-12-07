<?php
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;

$this->title = 'Есептер тізімі';
?>

<div class="reports-index p-5">
    <!-- Фильтры -->
    <div class="filter-section mb-5">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => ['index'],
            'id' => 'search-form'
        ]); ?>

        <div class="row g-4">
            <div class="col-lg-3">
                <?= Html::textInput('search', Yii::$app->request->get('search'), [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Шара атауы бойынша іздеу'
                ]) ?>
            </div>
            <div class="col-lg-3">
                <?= Select2::widget([
                    'name' => 'direction_id',
                    'data' => $directions,
                    'value' => Yii::$app->request->get('direction_id'),
                    'options' => [
                        'placeholder' => 'Бағытты таңдаңыз',
                        'class' => 'form-control-lg'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumResultsForSearch' => 10
                    ]
                ]) ?>
            </div>
            <div class="col-lg-3">
                <?= Select2::widget([
                    'name' => 'organization_id',
                    'data' => $organizations,
                    'value' => Yii::$app->request->get('organization_id'),
                    'options' => [
                        'placeholder' => 'Ұйымды таңдаңыз',
                        'class' => 'form-control-lg'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumResultsForSearch' => 10
                    ]
                ]) ?>
            </div>
        </div>

        <div class="row g-4 mt-3">
            <div class="col-lg-3">
                <?= DatePicker::widget([
                    'name' => 'date_from',
                    'type' => DatePicker::TYPE_INPUT,
                    'value' => Yii::$app->request->get('date_from'),
                    'language' => 'kk', // Установка казахского языка
                    'options' => [
                        'placeholder' => 'Басталу күні',
                        'class' => 'form-control form-control-lg'
                    ],
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy'
                    ]
                ]) ?>
            </div>
            <div class="col-lg-3">
                <?= DatePicker::widget([
                    'name' => 'date_to',
                    'value' => Yii::$app->request->get('date_to'),
                    'type' => DatePicker::TYPE_INPUT,
                    'language' => 'kk',
                    'options' => [
                        'placeholder' => 'Аяқталу күні',
                        'class' => 'form-control form-control-lg'
                    ],
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy'
                    ]
                ]) ?>
            </div>
            <div class="col-lg-6 text-end">
                <?= Html::submitButton('<i class="fa fa-search"></i>', ['class' => 'btn btn-primary btn-lg px-5']) ?>
                <?= Html::resetButton('<i class="fa fa-redo"></i>', ['class' => 'btn btn-outline-secondary btn-lg px-4']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div class="export-buttons">
            <?= Html::button('<i class="fa fa-file-word"></i> Word', [
                'class' => 'btn btn-outline-primary btn-lg me-3',
                'id' => 'export-word'
            ]) ?>
            <?= Html::button('<i class="fa fa-file-pdf"></i> PDF', [
                'class' => 'btn btn-outline-danger btn-lg',
                'id' => 'export-pdf'
            ]) ?>
        </div>
    </div>

    <!-- Таблица -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'tableOptions' => ['class' => 'table table-striped table-borderless align-middle'],
        'layout' => "{items}\n{pager}",
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions' => function($model) {
                    return ['class' => 'report-checkbox'];
                }
            ],
            [
                'attribute' => 'organization.name',
                'label' => 'Мешіт',
                'value' => function($model) {
                    return $model->organization->name['kk'] ?? '';
                }
            ],
            [
                'attribute' => 'direction.name',
                'label' => 'Бағыт',
                'value' => function($model) {
                    return $model->direction->name['kk'] ?? '';
                }
            ],
            [
                'attribute' => 'name',
                'label' => 'Шара атауы',
            ],
            [
                'attribute' => 'dateRange',
                'label' => 'Шараның атқарылған мерзімі',
            ],
            [
                'attribute' => 'people_count',
                'label' => 'Қамтылған адам саны',
            ],
            [
                'attribute' => 'link',
                'label' => 'Сілтеме',
            ],
            [
                'label' => 'Фотосуреттер',
                'format' => 'raw',
                'value' => function ($model) {
                    $images = $model->files;
                    if (empty($images)) {
                        return '';
                    }

                    $result = '<div class="image-preview">';
                    $count = 0;

                    foreach ($images as $image) {
                        if ($count < 4) {
                            $result .= Html::img($image->url, [
                                'alt' => 'Image',
                                'class' => 'img-thumbnail',
                                'style' => 'width:50px; height:auto; margin:5px; cursor:pointer;',
                                'onclick' => "openModal('modal-" . $model->id . "')"
                            ]);
                        }
                        $count++;
                    }

                    if (count($images) > 4) {
                        $result .= Html::button('...', [
                            'class' => 'btn btn-link',
                            'style' => 'padding: 0; margin-top: 5px;',
                            'onclick' => "openModal('modal-" . $model->id . "')"
                        ]);
                    }

                    $result .= '</div>';

                    // Генерация модального окна
                    $modalContent = '<div id="modal-' . $model->id . '" class="custom-modal">
                    <div class="custom-modal-content">
                        <span class="custom-modal-close" onclick="closeModal(\'modal-' . $model->id . '\')">&times;</span>';
                    foreach ($images as $image) {
                        $modalContent .= Html::img($image->url, [
                            'alt' => 'Image',
                            'class' => 'img-fluid',
                            'style' => 'margin:10px 0; width:100%; max-width:500px; display:block; margin:auto;',
                        ]);
                    }
                    $modalContent .= '</div></div>';

                    $result .= $modalContent;

                    return $result;
                }
            ],
        ],
    ]); ?>
</div>

<style>
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.8);
    }

    .custom-modal-content {
        background-color: #fff;
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 800px;
        text-align: center;
        border-radius: 10px;
    }

    .custom-modal img {
        max-width: 100%;
        height: auto;
        margin: 10px 0;
    }

    .custom-modal-close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .custom-modal-close:hover,
    .custom-modal-close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    body {
        background: #f5f7fa;
        font-family: 'Inter', sans-serif;
        color: #444;
        margin: 0;
        padding: 0;
    }

    .reports-index {
        margin: 40px auto;
        padding: 20px 30px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    /* Filter Section */
    .filter-section {
        background: #f9fafc;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 20px;
        border: 1px solid #e0e4eb;
        transition: all 0.3s ease-in-out;
    }

    .filter-section:hover {
        background: #eef1f7;
    }

    .filter-section input,
    .filter-section .select2-container--krajee .select2-selection {
        font-size: 16px;
        border: 1px solid #ccd1d9;
        border-radius: 8px;
        padding: 10px 15px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .filter-section input:focus,
    .filter-section .select2-container--krajee .select2-selection--single:focus {
        border-color: #007bff;
        box-shadow: 0 0 4px rgba(0, 123, 255, 0.3);
    }

    .filter-section .btn {
        font-size: 16px;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease-in-out;
    }

    .filter-section .btn:hover {
        transform: translateY(-2px);
        background: #007bff;
        color: #fff;
    }

    /* Table */
    .table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-top: 20px;
    }

    .table th {
        text-align: left;
        font-weight: 600;
        padding: 16px;
        background: #f2f4f8;
        border-bottom: 1px solid #d9dee4;
    }

    .table td {
        padding: 16px;
        border-bottom: 1px solid #eaeef2;
        color: #555;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background: #f9fafc;
    }

    .table-striped tbody tr:hover {
        background: #eef2f7;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        padding: 20px 0;
    }

    .pagination .page-link {
        border: 1px solid #ccd1d9;
        color: #007bff;
        margin: 0 5px;
        padding: 8px 16px;
        border-radius: 6px;
        transition: all 0.3s ease-in-out;
    }

    .pagination .page-link:hover {
        background: #007bff;
        color: #fff;
        border-color: #007bff;
    }

    /* Export Buttons */
    .export-buttons .btn {
        font-size: 14px;
        padding: 10px 20px;
        border-radius: 6px;
        margin-right: 10px;
        transition: all 0.3s ease-in-out;
    }

    .export-buttons .btn:hover {
        transform: translateY(-2px);
    }

    /* Selected Row Highlight */
    .selected-row {
        background: rgba(0, 123, 255, 0.1) !important;
    }

    /* Button Variants */
    .btn-outline-primary {
        color: #007bff;
        border: 1px solid #007bff;
        transition: all 0.3s;
    }

    .btn-outline-primary:hover {
        background: #007bff;
        color: #fff;
    }

    .btn-outline-danger {
        color: #dc3545;
        border: 1px solid #dc3545;
        transition: all 0.3s;
    }

    .btn-outline-danger:hover {
        background: #dc3545;
        color: #fff;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .filter-section {
            padding: 20px;
        }

        .table th, .table td {
            padding: 12px;
        }

        .pagination {
            flex-wrap: wrap;
        }

        .pagination .page-link {
            margin: 5px;
            padding: 6px 12px;
        }
    }

    @media (max-width: 768px) {
        .filter-section {
            padding: 15px;
        }

        .filter-section .btn {
            padding: 8px 16px;
        }

        .export-buttons .btn {
            font-size: 14px;
            padding: 8px 16px;
        }

        .table th, .table td {
            padding: 10px;
        }
    }

    @media (max-width: 576px) {
        .filter-section .btn {
            font-size: 14px;
            padding: 8px 12px;
        }

        .export-buttons .btn {
            font-size: 12px;
            padding: 6px 10px;
        }
    }


</style>

<?php
$js = <<<JS
    // Подсветка выбранных строк
    $('.report-checkbox').change(function() {
        $(this).closest('tr').toggleClass('selected-row', this.checked);
    });

    // Экспорт в Word
    $('#export-word').click(function() {
        exportSelected('word');
    });

    // Экспорт в PDF
    $('#export-pdf').click(function() {
        exportSelected('pdf');
    });

    function exportSelected(type) {
        var selected = $('.report-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        
        if (selected.length === 0) {
            alert('Жазбаларды таңдаңыз');
            return;
        }

        window.location.href = 'export?type=' + type + '&ids=' + selected.join(',');
    }
    
    
JS;
$this->registerJs($js);
?>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).style.display = "block";
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }

    // Закрытие модального окна при клике вне его
    window.onclick = function (event) {
        const modals = document.getElementsByClassName('custom-modal');
        for (let i = 0; i < modals.length; i++) {
            if (event.target === modals[i]) {
                modals[i].style.display = "none";
            }
        }
    }
</script>



