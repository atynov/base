<?php

$educationType = [
    [
        'title' => "Всего  государственных колледжей",
        'count' => 32,
    ],
    [
        'title' => "Коммунальное государственное казенное предприятие",
        'count' => 409,
    ],
    [
        'title' => "Коммунальное государственное учреждение",
        'count' => 319,
    ]
];




$organization = [
    [
        'title' => "КГУ «Балхашский колледж сервиса» Управления образования Карагандинской области",
        'info' => "КГУ «Бухар-жырауский агротехнический колледж» основан на базе ГУ «Профессионального лицея №18».  «Профессиональный лицей №18 в с. Ростовка Бухар-Жырауского района был  создан в  2008 году. КГУ «Бухар – жырауский агротехнический колледж»  включает в себя 3 учебных  пункта находящихся в п. Габидена Мустафина, с Ростовка, п. Көкпекті. Колледж готовит специалистов, конкурентоспособных  на рынке труда таких, как техническое обслуживание и ремонт автомобилей, продавец, повар, бухгалтер, мастер с/х производства (фермер), электрогазосварщик, для всех отраслей экономики региона.",
        "address" => "Карагандинская область, Бухар-жырауский район с. Ростовка ул. Центральная 12 ",
        "phone"=>"+7 (7213) 8-37-404",
        "fax" => "+7 (7213) 8-37-404",
        "email" => "gupl_18@mail.ru"

    ],
    [
        'title' => "КГУ «Балхашский колледж сервиса» Управления образования Карагандинской области",
        'info' => "КГУ «Бухар-жырауский агротехнический колледж» основан на базе ГУ «Профессионального лицея №18».  «Профессиональный лицей №18 в с. Ростовка Бухар-Жырауского района был  создан в  2008 году. КГУ «Бухар – жырауский агротехнический колледж»  включает в себя 3 учебных  пункта находящихся в п. Габидена Мустафина, с Ростовка, п. Көкпекті. Колледж готовит специалистов, конкурентоспособных  на рынке труда таких, как техническое обслуживание и ремонт автомобилей, продавец, повар, бухгалтер, мастер с/х производства (фермер), электрогазосварщик, для всех отраслей экономики региона.",
        "address" => "Карагандинская область, Бухар-жырауский район с. Ростовка ул. Центральная 12 ",
        "phone"=>"+7 (7213) 8-37-404",
        "fax" => "+7 (7213) 8-37-404",
        "email" => "gupl_18@mail.ru"
    ],
    [
        'title' => "КГУ «Балхашский колледж сервиса» Управления образования Карагандинской области",
        'info' => "КГУ «Бухар-жырауский агротехнический колледж» основан на базе ГУ «Профессионального лицея №18».  «Профессиональный лицей №18 в с. Ростовка Бухар-Жырауского района был  создан в  2008 году. КГУ «Бухар – жырауский агротехнический колледж»  включает в себя 3 учебных  пункта находящихся в п. Габидена Мустафина, с Ростовка, п. Көкпекті. Колледж готовит специалистов, конкурентоспособных  на рынке труда таких, как техническое обслуживание и ремонт автомобилей, продавец, повар, бухгалтер, мастер с/х производства (фермер), электрогазосварщик, для всех отраслей экономики региона.",
        'address' => "Карагандинская область, Бухар-жырауский район с. Ростовка ул. Центральная 12 ",
        'phone' =>"+7 (7213) 8-37-404",
        'fax' => "+7 (7213) 8-37-404",
        'email' => "gupl_18@mail.ru"
    ],
];

?>

<div class="d-flex flex-column">
    <h3 class="newsBlogPage"><?=$parent->nameLang ?></h3>
    <div class="d-flex justify-content-between mt-3">
        <?php foreach ($educationType as $item) { ?>
            <div class="educationViewBlog">
                <span class="educationViewBlogCount"><?=$item['count']?></span>
                <span class="educationViewBlogTitle"><?=$item['title']?></span>
            </div>
        <?php } ?>
    </div>
    <div class="educationSearch">
        <div class="educationSearchIcon">
            <i class="fa fa-search"></i>
        </div>
        <input type="search" class="form-control inputEducationSearch" placeholder="Поиск по организациям" aria-label="Поиск" aria-describedby="search-addon" />
    </div>
    <div id="accordion">

        <?php foreach($models as $key => $model) : ?>
            <div class="card mt-4">
                <div class="card-header p-0" id="headingOne">
                    <div class="educationAccordion" data-toggle="collapse" data-target="#collapse_<?= $key ?>">
                        <span><?=$model->nameLang?></span>
                    </div>
                </div>
                <div id="collapse_<?= $key ?>" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body d-flex flex-column pt-0">
                        <?php if (!empty($model->textLang)) : ?>
                            <span class="collapseSubtitle"><?= \Yii::t('app/modules/organization', 'Сведения об организации') ?></span>
                            <span class="collapseInfo"><?=$model->textLang?></span>
                        <?php endif; ?>

                        <?php if (!empty($model->address) || !empty($model->phone) || !empty($model->fax) || !empty($model->email)) : ?>
                            <span class="collapseSubtitle"><?= \Yii::t('app/modules/organization', 'Контакты') ?></span>
                            <div class="mb-3 collapseInfo">
                                <?php if (!empty($model->address)) : ?>
                                    <div>
                                        <span><?= \Yii::t('app/modules/organization', 'Адрес') ?>:</span>
                                        <span><?= $model->address ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($model->phone)) : ?>
                                    <div>
                                        <span><?= \Yii::t('app/modules/organization', 'Приемная') ?>:</span>
                                        <span><?=$model->phone?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($model->fax)) : ?>
                                    <div>
                                        <span><?= \Yii::t('app/modules/organization', 'Факс') ?>:</span>
                                        <span><?=$model->fax?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($model->email)) : ?>
                                    <div>
                                        <span><?= \Yii::t('app/modules/organization', 'Email') ?>:</span>
                                        <span><?=$model->email?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div>
                            <a target="_blank" href="https://<?=$model->alias?>.<?=strtolower($_SERVER['HTTP_HOST'])?>" class="btn btn-outline-primary btn-sm"><?= \Yii::t('app/modules/organization', 'Перейти на страницу') ?></a>
                            <button type="button" class="btn btn-outline-primary btn-sm"><?= \Yii::t('app/modules/organization', 'Руководство') ?></button>
                            <button type="button" class="btn btn-outline-primary btn-sm"><?= \Yii::t('app/modules/organization', 'Онлайн-приемная') ?></button>
                        </div>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>

        <?php foreach($organization as $key=>$item){ ?>
            <div class="card mt-4">
                <div class="card-header p-0" id="headingOne">
                    <div class="educationAccordion" data-toggle="collapse" data-target="#collapse<?php echo $key ?>">
                        <span><?=$item['title']?></span>
                    </div>
                </div>
                <div id="collapse<?php echo $key ?>" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body d-flex flex-column pt-0">
                        <span class="collapseSubtitle"><?= \Yii::t('app/modules/organization', 'Сведения об организации') ?></span>
                        <span class="collapseInfo"><?=$item['info']?></span>
                        <span class="collapseSubtitle"><?= \Yii::t('app/modules/organization', 'Контакты') ?></span>
                        <div class="mb-3 collapseInfo">
                            <div>
                                <span><?= \Yii::t('app/modules/organization', 'Адрес') ?>:</span>
                                <span><?=$item['address']?></span>
                            </div>
                            <div>
                                <span><?= \Yii::t('app/modules/organization', 'Приемная') ?>:</span>
                                <span><?=$item['phone']?></span>
                            </div>
                            <div>
                                <span><?= \Yii::t('app/modules/organization', 'Факс') ?>:</span>
                                <span><?=$item['fax']?></span>
                            </div>
                            <div>
                                <span><?= \Yii::t('app/modules/organization', 'Email') ?>:</span>
                                <span><?=$item['email']?></span>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-primary btn-sm"><?= \Yii::t('app/modules/organization', 'Перейти на страницу') ?></button>
                            <button type="button" class="btn btn-outline-primary btn-sm"><?= \Yii::t('app/modules/organization', 'Руководство') ?></button>
                            <button type="button" class="btn btn-outline-primary btn-sm"><?= \Yii::t('app/modules/organization', 'Онлайн-приемная') ?></button>
                        </div>
                    </div>
                </div>

            </div>
        <?php } ?>
    </div>
</div>
