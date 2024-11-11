<?php

use yii\helpers\Url;

use modules\rbac\models\Permission;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Home');
$this->params['title']['small'] = Yii::t('app', 'Dashboard');
/** @var yii\web\User $user */
$user = Yii::$app->user;
?>

<div class="default-backend-default-index">
    <div class="box">
        <?php if ($user->can(Permission::PERMISSION_MANAGER_USERS)) : ?>
            <div class="col-md-4">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= Yii::t('app', 'Users') ?></h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                    title="<?= Yii::t('app', 'Collapse') ?>">
                                <i class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip"
                                    title="<?= Yii::t('app', 'Remove') ?>">
                                <i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <a class="btn btn-app" href="<?= Url::to(['/users/default/index']) ?>">
                            <i class="fa fa-users"></i> <?= Yii::t('app', 'Users') ?>
                        </a>
                        <?php if ($user->can(Permission::PERMISSION_MANAGER_RBAC)) : ?>
                            <a class="btn btn-app" href="<?= Url::to(['/rbac/default/index']) ?>">
                                <i class="fa fa-unlock"></i> <?= Yii::t('app', 'RBAC') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>
