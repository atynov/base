<?php

/** @var yii\web\View $this */
/** @var modules\reports\models\Report $model */
/** @var modules\reports\models\Direction[] $directions */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Өзгерту';
$currentMonth = (int)date('m');
$currentYear = (int)date('Y');
$months = [
    1 => 'Қаңтар',
    2 => 'Ақпан',
    3 => 'Наурыз',
    4 => 'Сәуір',
    5 => 'Мамыр',
    6 => 'Маусым',
    7 => 'Шілде',
    8 => 'Тамыз',
    9 => 'Қыркүйек',
    10 => 'Қазан',
    11 => 'Қараша',
    12 => 'Желтоқсан',
];
$years = [
    $currentYear - 1 => $currentYear - 1,
    $currentYear => $currentYear,
    $currentYear + 1 => $currentYear + 1,
];
?>

<div class="reports-create">
    <div class="p-2 mb-4 bg-light rounded">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="fas fa-arrow-left"></i> Артқа', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <div class="report-form">
        <?php $form = ActiveForm::begin(['id' => 'report-form']); ?>

        <?= $form->field($model, 'month')->dropDownList(
            $months,
            [
                'prompt' => 'Айды таңдаңыз',
                'id' => 'month-select',
                'options' => [$currentMonth => ['Selected' => true]], // Текущий месяц по умолчанию
            ]
        )->label('Айы') ?>

        <div class="form-check mb-3">
            <?= Html::checkbox('Report[period_type]', false, [
                'id' => 'period_type',
                'class' => 'form-check-input',
            ]) ?>
            <?= Html::label('Жылдық есеп', 'period_type', ['class' => 'form-check-label']) ?>
        </div>

        <?= $form->field($model, 'year')->dropDownList(
            $years,
            [
                'prompt' => 'Жылды таңдаңыз',
                'id' => 'year-select',
                'options' => [$currentYear => ['Selected' => true]], // Текущий год по умолчанию
            ]
        )->label('Жылы') ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'direction_id')->dropDownList(
            \yii\helpers\ArrayHelper::map(
                $directions,
                'id',
                function ($direction) {
                    $name = $direction['name']; // Декодируем JSON
                    return  $name['kk']; // Если язык недоступен, используем 'kk' как fallback
                }
            ),
            ['prompt' => 'Бағытты таңдаңыз']
        ) ?>

        <?= $form->field($model, 'status')->dropDownList([
            1 => 'Орындалуда',
            2 => 'Орындалды',
            3 => 'Орындалмады',
        ], ['prompt' => 'Күйін таңдаңыз']) ?>

        <div class="form-group row">
            <label class="col-form-label" for="start_date"><?= Html::encode('Орындалу мерзімі') ?></label>
            <div class="col d-flex align-items-center gap-3">
                <?= $form->field($model, 'start_date', [
                    'options' => ['class' => 'mb-0'], // Убираем лишний отступ
                    'template' => '{input}{error}', // Убираем отдельный лейбл
                ])->input('date', [
                    'class' => 'form-control',
                    'id' => 'start_date', // Добавляем ID для JavaScript
                ])->label(false) ?>

                <?= $form->field($model, 'end_date', [
                    'options' => ['class' => 'mb-0'], // Убираем лишний отступ
                    'template' => '{input}{error}', // Убираем отдельный лейбл
                ])->input('date', [
                    'class' => 'form-control',
                    'id' => 'end_date', // Добавляем ID для JavaScript
                ])->label(false) ?>
            </div>
        </div>

        <?= $form->field($model, 'people_count')->textInput([
            'type' => 'number', // Ограничивает ввод на уровне HTML
            'min' => 0, // Указывает минимальное значение
            'step' => 1, // Указывает шаг (только целые числа)
            'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')" // JavaScript для проверки ввода
        ]) ?>

        <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>

        <!-- Скрытые поля для отправки URL загруженных и удаленных изображений -->

        <p></p>
        <?= \common\widgets\ImageUploadWidget::widget([
            'uploadUrl' => \yii\helpers\Url::to(['/upload/image']), // Укажите URL загрузки
            'model' => $model,
            'attribute' => 'imageFiles', // Поле для хранения изображений
            'multiple' => true, // Разрешаем множественную загрузку
            'existingImages' => $existingImages, // Уже загруженные изображения
            'submit' => 'form-submit', // ID кнопки отправки
            'form' => 'report-form', // ID формы
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Сақтау', ['id'=>'form-submit','class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$this->registerJs(<<<JS
$('#report-form').on('submit', function(e) {
    let isValid = true;
    
    // Проверка обязательных полей
    if ($('#report-direction_id').val() === '') {
        isValid = false;
        alert('Бағытты таңдаңыз.');
    }
    
    if ($('#report-name').val() === '' && isValid) {
        isValid = false;
        alert('Шара атауын енгізіңіз.');
    }
    
    if ($('#report-status').val() === '' && isValid) {
        isValid = false;
        alert('Статус таңдаңыз.');
    }
    
    if ($('#report-start_date').val() === '' && isValid && $('#report-status').val()!=3) {
        isValid = false;
        alert('Орындалу мерзімін енгізіңіз.');
    }
    
    if ($('#report-people_count').val() === '' && isValid  && $('#report-status').val()!=3) {
        isValid = false;
        alert('Қамтылған адам санын енгізіңіз.');
    }
    
    if ($('#report-description').val() === '' && isValid  && $('#report-status').val()==3) {
        isValid = false;
        alert('Себебін сипаттамада енгізіңіз.');
    }

    if (!isValid) {
        e.preventDefault(); // Отменяем отправку формы если есть ошибки валидации
    }
});
JS);
?>

