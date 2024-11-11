<?php

namespace common\components;

use yii\base\Model;

class FormModel extends Model
{

    // сценарии
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_ADMIN_CREATE = 'adminCreate';

    public $uniqueAttributes = [];


    public $scenario;

    /**
     * Метод хранящий описания атрибутов:
     *
     * @return array описания атрибутов
     **/
    public function attributeDescriptions()
    {
        return [];
    }

    /**
     * Метод получения описания атрибутов
     *
     * @param string $attribute - id-атрибута
     *
     * @return string описания атрибутов
     **/
    public function getAttributeDescription($attribute)
    {
        $descriptions = $this->attributeDescriptions();

        return (isset($descriptions[$attribute])) ? $descriptions[$attribute] : '';
    }


    public function hasAttribute($name)
    {
        return isset($this->_attributes[$name]) || in_array($name, $this->attributes(), true);
    }


    public function rules()
    {

        $rules = [];

        if ($this->hasAttribute('status'))
            $rules[] = ['status', 'boolean'];

        if ($this->hasAttribute('in_sitemap'))
            $rules[] = ['in_sitemap', 'boolean'];

        if ($this->hasAttribute('in_rss'))
            $rules[] = ['in_rss', 'boolean'];

        if ($this->hasAttribute('in_turbo'))
            $rules[] = ['in_turbo', 'boolean'];

        if ($this->hasAttribute('in_amp'))
            $rules[] = ['in_amp', 'boolean'];

        if ($this->hasAttribute('name'))
            $rules[] = ['name', 'string', 'max' => 255];


        if ($this->hasAttribute('alias')) {
            $rules[] = ['alias', 'string', 'max' => 255];
            $rules[] = ['alias', 'match', 'pattern' => '/^[A-Za-z0-9\-\_]+$/', 'message' => \Yii::t('app','It allowed only Latin alphabet, numbers and the «-», «_» characters.')];

            if (in_array('alias', $this->uniqueAttributes)) {
                $rules[] = ['alias', 'unique', 'message' => \Yii::t('app', 'Attribute must be unique.')];
            }
        }

        if ($this->hasAttribute('url'))
            $rules[] = ['url', 'safe'];

        if ($this->hasAttribute('route')) {
            $rules[] = ['route', 'string', 'max' => 255];
            $rules[] = ['route', 'match', 'pattern' => '/^[A-Za-z0-9\-\_\/]+$/', 'message' => \Yii::t('app','It allowed only Latin alphabet, numbers and the «-», «_», «/» characters.')];
        }

        if ($this->hasAttribute('layout')) {
            $rules[] = ['layout', 'string', 'max' => 64];
            $rules[] = ['layout', 'match', 'pattern' => '/^[A-Za-z0-9\-\_\/\@]+$/', 'message' => \Yii::t('app','It allowed only Latin alphabet, numbers and the «@», «-», «_», «/» characters.')];
        }

        return $rules;
    }


}
