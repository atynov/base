<?php
use yii\helpers\Html;
?>

    <div class="newsCart mr-0 mt-4">
        <a href="<?= \yii\helpers\Url::to(['/news/' . $model->alias]) ?>" >
            <?= ($model->image ? Html::img($model->getImagePath(true) . '/' . $model->image, ['class' => 'newsImgSize']) : ''); ?>
        </a>

        <div class="p-2 d-flex flex-column justify-content-between">
            <span class="newsBodyTitle">
                <a href="<?= \yii\helpers\Url::to(['/news/' . $model->alias]) ?>">
                    <?= Html::encode($model->name); ?>
                </a>
            </span>
            <div class="newBodyBottom">
                <div>
                    <i class="fa fa-eye mr-2" aria-hidden="true"></i>
                    <span><?php /* $model->views */ ?></span>
                </div>
                <span class="newsDate"><?=$model->created_at?></span>
            </div>
        </div>

    </div>
