<?php

namespace modules\organization\traits;

use Yii;
use modules\organization\Module;

/**
 * Trait ModuleTrait
 *
 * @property-read Module $module
 * @package modules\organization\traits
 */
trait ModuleTrait
{
    /**
     * @return null|\yii\base\Module
     */
    public function getModule()
    {
        return Yii::$app->getModule('organization');
    }
}
