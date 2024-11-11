<?php

use common\widgets\AliasInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $model modules\news\models\News */
/* @var $form yii\widgets\ActiveForm */

if ($model->scenario == $model::SCENARIO_CREATE) {
    $this->title = Yii::t('app/modules/news', 'New post');
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/news', 'All news'), 'url' => ['news/index']];
    $this->params['breadcrumbs'][] = $this->title;
} else {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/news', 'All news'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => StringHelper::stringShorter($model->name, 64), 'url' => ['view', 'id' => $model->id]];
    $this->params['breadcrumbs'][] = Yii::t('app/modules/news', 'Updating');
}
?>

<?php if (Yii::$app->authManager && Yii::$app->user->can('managerPosts')) : ?>
    <div class="news-<?= $model->scenario ?>">
        <div class="news-form">
            <?php $form = ActiveForm::begin([
                'id' => "addNewsForm",
                'enableAjaxValidation' => true,
                'options' => [
                    'enctype' => 'multipart/form-data'
                ]
            ]); ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>


            <?= $form->field($model, 'category_id')->dropDownList($model->getCategoriesList(), [
                'options' => [
                    'class' => 'form-control'
                ]
            ])->label(Yii::t('app/modules/news', 'Category')); ?>

            <?= $form->field($model, 'alias')->widget(AliasInput::class, [
                'labels' => [
                    'edit' => Yii::t('app/modules/news', 'Edit'),
                    'save' => Yii::t('app/modules/news', 'Save')
                ],
                'options' => [
                    'baseUrl' => ($model->id) ? $model->url : ''
                ]
            ])->label(Yii::t('app/modules/news', 'News URL')); ?>

            <?php
                if (isset(Yii::$app->redirects) && $model->url && ($model->status == $model::STATUS_PUBLISHED)) {
                    if ($url = Yii::$app->redirects->check($model->url, false)) {
                        echo Html::tag('div', Yii::t('app/modules/redirects', 'For this URL is active redirect to {url}', [
                            'url' => $url
                        ]), [
                            'class' => "alert alert-warning"
                        ]);
                    }
                }
            ?>

            <?= $form->field($model, 'excerpt')->textarea(['rows' => 3]) ?>
            <?= $form->field($model, 'content')->textarea(['rows' => 5]) ?>

            <?php
                if ($model->image) {
                    echo '<div class="row">';
                    echo '<div class="col-xs-12 col-sm-3 col-md-2">' . Html::img($model->getImagePath(true) . '/' . $model->image, ['class' => 'img-responsive']) . '</div>';
                    echo '<div class="col-xs-12 col-sm-9 col-md-10">' . $form->field($model, 'file')->fileInput() . '</div>';
                    echo '</div><br/>';
                } else {
                    echo $form->field($model, 'file')->fileInput();
                }
            ?>

            <?php /* ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h6 class="panel-title">
                        <a data-toggle="collapse" href="#newsMetaTags">
                            <?= Yii::t('app/modules/news', "SEO") ?>
                        </a>
                    </h6>
                </div>
                <div id="newsMetaTags" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?= $form->field($model, 'title')->textInput() ?>
                        <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
                        <?= $form->field($model, 'keywords')->textarea(['rows' => 3]) ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h6 class="panel-title">
                        <a data-toggle="collapse" href="#newsOptions">
                            <?= Yii::t('app/modules/news', "Other options") ?>
                        </a>
                    </h6>
                </div>
                <div id="newsOptions" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?= $form->field($model, 'in_sitemap', [
                                'template' => "{label}\n<br/>{input}\n{hint}\n{error}"
                            ])
                            ->checkbox(['label' => Yii::t('app/modules/news', '- display in the sitemap')])
                            ->label(Yii::t('app/modules/news', 'Sitemap'))
                        ?>
                        <?= $form->field($model, 'in_rss', [
                            'template' => "{label}\n<br/>{input}\n{hint}\n{error}"
                        ])->checkbox(['label' => Yii::t('app/modules/news', '- display in the rss-feed')])->label(Yii::t('app/modules/news', 'RSS-feed'))
                        ?>
                        <?= $form->field($model, 'in_turbo', [
                            'template' => "{label}\n<br/>{input}\n{hint}\n{error}"
                        ])->checkbox(['label' => Yii::t('app/modules/news', '- display in the turbo-pages')])->label(Yii::t('app/modules/news', 'Yandex turbo'))
                        ?>
                        <?= $form->field($model, 'in_amp', [
                            'template' => "{label}\n<br/>{input}\n{hint}\n{error}"
                        ])->checkbox(['label' => Yii::t('app/modules/news', '- display in the AMP pages')])->label(Yii::t('app/modules/news', 'Google AMP'))
                        ?>
                    </div>
                </div>
            </div>
            <?php */ ?>

            <?= $form->field($model, 'public_at')->widget(\kartik\datetime\DateTimePicker::class, [
                'options' => ['placeholder' => '...'],
                'pluginOptions' => [
                    'todayHighlight' => true
                ]
            ]); ?>

            <?= $form->field($model, 'status')->dropDownList($model->getStatusesList()); ?>

            <?= $form->field($model, 'source_url')->textInput(['maxlength' => true]) ?>
            <hr/>
            <div class="form-group">
                <?= Html::a(Yii::t('app/modules/news', '&larr; Back to list'), ['news/index'], ['class' => 'btn btn-default pull-left']) ?>
                <?php if ((Yii::$app->authManager  && Yii::$app->user->can('managerPosts', [
                    'created_by' => $model->created_by,
                    'updated_by' => $model->updated_by
                ])) || !$model->id) : ?>&nbsp;
                    <?= Html::submitButton(Yii::t('app/modules/news', 'Save'), ['class' => 'btn btn-save btn-success pull-right']) ?>
                <?php endif; ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php $this->registerJs(<<< JS
        $(document).ready(function() {
            function afterValidateAttribute(event, attribute, messages)
            {
                if (attribute.name && !attribute.alias && messages.length == 0) {
                    var form = $(event.target);
                    $.ajax({
                        type: form.attr('method'),
                        url: form.attr('action'),
                        data: form.serializeArray(),
                    }).done(function(data) {
                        if (data.alias && form.find('#news-alias').val().length == 0) {
                            form.find('#news-alias').val(data.alias);
                            form.find('#news-alias').change();
                            form.yiiActiveForm('validateAttribute', 'news-alias');
                        }
                    }).fail(function () {
                        /*form.find('#options-type').val("");
                        form.find('#options-type').trigger('change');*/
                    });
                    return false; // prevent default form submission
                }
            }
            $("#addNewsForm").on("afterValidateAttribute", afterValidateAttribute);
        });
JS
    ); ?>
<?php else: ?>
    <div class="page-header">
        <h1 class="text-danger"><?= Yii::t('app/modules/news', 'Error {code}. Access Denied', [
                'code' => 403
            ]) ?> </h1>
    </div>
    <div class="news-update-error">
        <blockquote>
            <?= Yii::t('app/modules/news', 'You are not allowed to view this page.'); ?>
        </blockquote>
    </div>
<?php endif; ?>
