
<h3 class="newsBlogPage"><?=$parent->nameLang ?></h3>
<div class="mt-3">
    <div class="educationListBody">
        <?php foreach($models as $item) { ?>
            <div class="educationLink">
                <div class="d-flex align-items-center">
                    <div class="educationLinkCount"><?= rand(0,20)/*$item['count']*/?></div>
                    <span class="educationLinkTitle">
                            <a href="<?= \yii\helpers\Url::to(['/cats/' . $parent->alias . '/' . $item->alias]) ?>">
                                <?=$item->nameLang ?>
                            </a>
                        </span>
                </div>
                <span class="educationLinkArrow"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
            </div>
        <?php } ?>
    </div>
    <div>

    </div>
</div>
