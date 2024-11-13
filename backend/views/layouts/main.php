<?php

use backend\assets\AppAsset;
use backend\assets\plugins\iCheckAsset;
use backend\widgets\control\ControlSidebar;
use backend\widgets\search\SearchSidebar;
use modules\users\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Menu;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use modules\users\widgets\AvatarWidget;
use modules\users\Module as UserModule;

/* @var $this View */
/* @var $content string */

iCheckAsset::register($this);
AppAsset::register($this);

/** @var yii\web\User $user */
$user = Yii::$app->user;
/** @var User $identity */
$identity = $user->identity;
$fullUserName = ($identity !== null) ? $identity->getUserFullName() : Yii::t('app', 'Авторланбаған');
$assetManager = Yii::$app->assetManager;
$formatter = Yii::$app->formatter;
$homeUrl = is_string(Yii::$app->homeUrl) ? Yii::$app->homeUrl : '/';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body class="hold-transition skin-blue sidebar-mini">
<?php $this->beginBody() ?>

<div class="wrapper">
    <header class="main-header">
        <a href="<?= $homeUrl ?>" class="logo">
            <span class="logo-mini"><b>A</b>LT</span>
            <span class="logo-lg"><b>Әкімшілік</b> Панель</span>
        </a>
        <nav class="navbar navbar-static-top">
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Навигацияны ашу</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <?= AvatarWidget::widget(['imageOptions' => ['class' => 'user-image']]) ?>
                            <span class="hidden-xs"><?= $fullUserName ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="user-header">
                                <?= AvatarWidget::widget() ?>
                                <p><?= $fullUserName ?><small><?= Yii::t('app', 'Қосылған күн:') ?> <?= $formatter->asDatetime($identity->created_at, 'LLL yyyy') ?></small></p>
                            </li>
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="<?= Url::to(['/users/profile/index']) ?>" class="btn btn-default btn-flat"><?= Yii::t('app', 'Профиль') ?></a>
                                </div>
                                <div class="pull-right">
                                    <?= Html::beginForm(['/users/default/logout']) . Html::submitButton(Yii::t('app', 'Шығу'), ['class' => 'btn btn-default btn-flat logout']) . Html::endForm() ?>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <aside class="main-sidebar">
        <section class="sidebar">
            <div class="user-panel">
                <div class="pull-left image">
                    <?= AvatarWidget::widget() ?>
                </div>
                <div class="pull-left info">
                    <p><?= $fullUserName ?></p>
                    <a href="#"><i class="fa fa-circle text-success"></i> <?= Yii::t('app', 'Қосылып тұр') ?></a>
                </div>
            </div>

            <?= SearchSidebar::widget(['status' => true]) ?>

            <?php
            $items = [
                [
                    'label' => Yii::t('app', 'БАСТЫ БӨЛІМДЕР'),
                    'options' => ['class' => 'header']
                ]
            ];

            if (Yii::$app->modules) {
                foreach (Yii::$app->modules as $module) {
                    if (is_object($module) && isset($module->isBackend) && $module->isBackend) {
                        $items[] = $module::backendMenuItems();
                    } elseif (is_array($module) && isset($module['isBackend']) && $module['isBackend']) {
                        if (method_exists($module['class'], 'backendMenuItems')) {
                            $items[] = $module['class']::backendMenuItems();
                        }
                    }
                }
            }

            echo Menu::widget([
                'options' => ['class' => 'sidebar-menu'],
                'encodeLabels' => false,
                'submenuTemplate' => "\n<ul class='treeview-menu'>\n{items}\n</ul>\n",
                'activateParents' => true,
                'items' => $items
            ]);
            ?>
        </section>
    </aside>

    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                <?php
                $small = isset($this->params['title']['small']) ? ' ' . Html::tag('small', Html::encode($this->params['title']['small'])) : '';
                echo Html::encode($this->title) . $small ?>
            </h1>
            <?= Breadcrumbs::widget([
                'homeLink' => ['label' => '<i class="fa fa-dashboard"></i> ' . Yii::t('app', 'Басты бет'), 'url' => Url::to(['/main/default/index'])],
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                'encodeLabels' => false
            ]) ?>
            <?= Alert::widget() ?>
        </section>
        <section class="content">
            <?= $content ?>
        </section>
    </div>

    <footer class="main-footer">
        <div class="pull-right hidden-xs"></div>
        <strong>&copy; <?= date('Y') ?> <a href="#"><?= Yii::$app->name ?></a>.</strong> <?= Yii::t('app', 'Барлық құқықтар қорғалған.') ?>
    </footer>

    <?= ControlSidebar::widget(['status' => true, 'demo' => false]) ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
