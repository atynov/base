<?php

namespace common\components;

use modules\organization\models\Organization;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use common\models\User;
use yii\helpers\Html;

class ActiveRecord extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 2;
    const STATUS_ACTIVE = 1;
    const STATUS_WAIT = 3;

    // сценарии
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public static function find()
    {
        return Yii::createObject(ActiveQuery::className(), [get_called_class()]);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        if ($this->hasAttribute('created_at')) {
            $behaviors = ArrayHelper::merge([
                'timestamp' => [
                    'class' => TimestampBehavior::class,
                    'attributes' => [
                        self::EVENT_BEFORE_INSERT => 'created_at',
                    ],
                    'value' => new Expression('NOW()'),
                ]
            ], $behaviors);
        }

        if ($this->hasAttribute('updated_at')) {
            $behaviors = ArrayHelper::merge([
                'timestamp' => [
                    'class' => TimestampBehavior::class,
                    'attributes' => [
                        self::EVENT_BEFORE_UPDATE => 'updated_at',
                    ],
                    'value' => new Expression('NOW()'),
                ]
            ], $behaviors);
        }

        if ($this->hasAttribute('created_by')) {
            $behaviors = ArrayHelper::merge([
                'blameable' => [
                    'class' => BlameableBehavior::class,
                    'createdByAttribute' => 'created_by',
                ]
            ], $behaviors);
        }

        if ($this->hasAttribute('updated_by')) {
            $behaviors = ArrayHelper::merge([
                'blameable' => [
                    'class' => BlameableBehavior::class,
                    'updatedByAttribute' => 'updated_by',
                ]
            ], $behaviors);
        }

        if ($this->hasAttribute('name') && $this->hasAttribute('alias')) {
            $behaviors = ArrayHelper::merge([
                'sluggable' => [
                    'class' => SluggableBehavior::class,
                    'attribute' => ['name'],
                    'slugAttribute' => 'alias',
                    'immutable' => true
                ]
            ], $behaviors);
        }

        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();

        if ($this->hasAttribute('created_at'))
            $rules[] = ['created_at', 'safe'];

        if ($this->hasAttribute('updated_at'))
            $rules[] = ['updated_at', 'safe'];

        if ($this->hasAttribute('created_by')) {
            $rules[] = ['created_by', 'safe'];

            if (class_exists('common\models\User'))
                $rules[] = ['created_by', 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']];
        }

        if ($this->hasAttribute('updated_by')) {
            $rules[] = ['updated_by', 'safe'];

            if (class_exists('common\models\User'))
                $rules[] = ['updated_by', 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']];
        }

        if ($this->hasAttribute('status'))
            $rules[] = ['status', 'integer'];

        if ($this->hasAttribute('name'))
            $rules[] = ['name', 'string', 'max' => 255];

        if ($this->hasAttribute('alias')) {
            $rules[] = ['alias', 'string', 'max' => 255];
            $rules[] = ['alias', 'match', 'pattern' => '/^[A-Za-z0-9\-\_]+$/', 'message' => Yii::t('app','It allowed only Latin alphabet, numbers and the «-», «_» characters.')];

            // $rules[] = ['alias', 'unique', 'message' => Yii::t('app', 'Attribute must be unique.')];
        }

        if ($this->hasAttribute('organization_id')) {
            $rules[] = ['organization_id', 'integer'];
        }

        if ($this->hasAttribute('orderby')) {
            $rules[] = ['orderby', 'integer'];
        }

        return $rules;
    }


    public function beforeSave($insert)
    {

        if ($this->hasAttribute('organization_id')) {
            if (empty($this->organization_id)) {
                $this->organization_id = Organization::getCurrentOrganizationId();
            }
        }

        return parent::beforeSave($insert);
    }


    public function scenarios()
    {
        return array_merge(parent::scenarios(),
            [
                self::SCENARIO_CREATE => $this->attributesForSave(self::SCENARIO_CREATE),
                self::SCENARIO_UPDATE => $this->attributesForSave(self::SCENARIO_UPDATE),
            ]);
    }

    public function attributesForSave($scenario)
    {

//        if (static::tableName() == '{{%active_record}}') {
//            return [];
//        }

        $attributes = $this->attributes();

        foreach ($attributes as $index => $attr) {
            $primary = self::primaryKey();
            if (\is_array($primary)) {
                foreach ($primary as $pk) {
                    if ($attr == $pk) {
                        unset($attributes[$index]);
                    }
                }
            } elseif ($attr == $primary) {
                unset($attributes[$index]);
            }
        }

        return $attributes;
    }

    public function getNameLang()
    {
        return (!empty($this->name_lang) && is_array($this->name_lang) && array_key_exists(Yii::$app->language, $this->name_lang) ? $this->name_lang[Yii::$app->language] : $this->name);
    }

    public function getTextLang()
    {
        return (!empty($this->text_lang) && is_array($this->text_lang) && array_key_exists(Yii::$app->language, $this->text_lang) ? $this->text_lang[Yii::$app->language] : $this->text);
    }


    public static function getStatusList()
    {
        return [
//            self::STATUS_DRAFT => Yii::t('yii', 'Черновик'),
//            self::STATUS_PUBLISHED => Yii::t('yii', 'Опубликован'),
//            self::STATUS_SCHEDULED => Yii::t('yii', 'Scheduled'),
//            self::STATUS_MODERATED => Yii::t('yii', 'Проверен'),
            self::STATUS_ACTIVE => Yii::t('yii', 'Активный'),
            self::STATUS_INACTIVE => Yii::t('yii', 'Не активный'),
            self::STATUS_DELETED => Yii::t('yii', 'Удален'),
        ];
    }

    public function setParams($name, $value)
    {
        $jParams = $this->paramsJson;
        if (!$jParams) {
            $jParams = [];
        }
        $jParams[$name] = $value;
        $this->params = $jParams;
    }


    /**
     * @return array
     */
    public static function getStatusesArray()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('yii', 'Активный'),
            self::STATUS_WAIT => Yii::t('yii', 'В ожидании'),
            self::STATUS_DELETED => Yii::t('yii', 'Удален'),
        ];
    }

    /**
     * @return array
     */
    public static function getLabelsArray()
    {
        return [
            self::STATUS_ACTIVE => 'success',
            self::STATUS_INACTIVE => 'warning',
            self::STATUS_DELETED => 'danger',
        ];
    }

    /**
     * @return mixed
     */
    public function getStatusName()
    {
        return \yii\helpers\ArrayHelper::getValue(self::getStatusesArray(), $this->status);
    }

    public function __get($name)
    {
        if (substr($name, \strlen($name) - 4, 4) == 'Json') {
            $name = substr($name, 0, -4);
            $attr = parent::__get($name);
            if ($attr)
                return \is_array($attr) ? $attr : (json_decode($attr, true) ?: []);
        }

        return parent::__get($name);
    }

    /**
     * Return <span class="label label-success">Active</span>
     * @return string
     */
    public function getStatusLabelName()
    {
        $name = ArrayHelper::getValue(self::getLabelsArray(), $this->status);
        return Html::tag('span', $this->getStatusName(), ['class' => 'label label-' . $name]);
    }


}
