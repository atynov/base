<?php

namespace modules\organization\models;

use common\components\ActiveRecord;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\base\InvalidArgumentException;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "{{%organization_category}}".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string $alias
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 * @property integer $orderby
 */

class OrganizationCategory extends ActiveRecord
{

    const DEFAULT_CATEGORY_ID = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%organization_category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => new Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'sluggable' =>  [
                'class' => SluggableBehavior::class,
                'attribute' => ['name'],
                'slugAttribute' => 'alias',
                'ensureUnique' => true,
                'skipOnEmpty' => true,
                'immutable' => true,
                'value' => function ($event) {
                    return mb_substr($this->name, 0, 32);
                }
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['name', 'alias'], 'required'],
            [['parent_id', 'orderby'], 'integer'],
            [['name', 'alias'], 'string', 'min' => 3, 'max' => 128],

            [['name_lang', 'text_lang'], 'safe'],

            ['alias', 'unique', 'message' => Yii::t('app/modules/organization', 'Param attribute must be unique.')],
            ['alias', 'match', 'pattern' => '/^[A-Za-z0-9\-\_]+$/', 'message' => Yii::t('app/modules/organization','It allowed only Latin alphabet, numbers and the «-», «_» characters.')],
            [['created_at', 'updated_at'], 'safe'],
        ];

        if (class_exists('\modules\users\models\User')) {
            $rules[] = [['created_by', 'updated_by'], 'safe'];
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/modules/organization', 'ID'),
            'parent_id' => Yii::t('app/modules/organization', 'Parent ID'),
            'name' => Yii::t('app/modules/organization', 'Name'),
            'alias' => Yii::t('app/modules/organization', 'Alias'),
            'title' => Yii::t('app/modules/organization', 'Title'),
            'description' => Yii::t('app/modules/organization', 'Description'),
            'keywords' => Yii::t('app/modules/organization', 'Keywords'),
            'created_at' => Yii::t('app/modules/organization', 'Created at'),
            'created_by' => Yii::t('app/modules/organization', 'Created by'),
            'updated_at' => Yii::t('app/modules/organization', 'Updated at'),
            'updated_by' => Yii::t('app/modules/organization', 'Updated by'),
        ];
    }

    public function beforeDelete()
    {
        // Category for uncategorized posts has undeleted
        if ($this->id === self::DEFAULT_CATEGORY_ID)
            return false;

        // Set default uncategorized category for organization items
        Organization::updateAll(['status' => Organization::STATUS_DRAFT, 'cat_id' => self::DEFAULT_CATEGORY_ID], ['cat_id' => $this->id]);

        return parent::beforeDelete();
    }

    /**
     * @return object of \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        if (class_exists('\modules\users\models\User'))
            return $this->hasOne(\modules\users\models\User::class, ['id' => 'created_by']);
        else
            return $this->created_by;
    }

    /**
     * @return object of \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        if (class_exists('\modules\users\models\User'))
            return $this->hasOne(\modules\users\models\User::class, ['id' => 'updated_by']);
        else
            return $this->updated_by;
    }


    /**
     *
     * @param $withScheme boolean, absolute or relative URL
     * @return string or null
     */
    public function getCategoryUrl($withScheme = true, $realUrl = false)
    {
        $this->route = $this->getRoute();
        if (isset($this->alias)) {
            return \yii\helpers\Url::to($this->route . '/' .$this->alias, $withScheme);
        } else {
            return null;
        }
    }

    /**
     * Returns the URL to the view of the current organization category
     *
     * @return string
     */
//    public function getUrl()
//    {
//        if ($this->url === null)
//            $this->url = $this->getCategoryUrl();
//
//        return $this->url;
//    }


    /**
     * @param bool $allLabel
     * @param bool $rootLabel
     * @return array
     */
    public function getParentsList($allLabel = true, $rootLabel = false)
    {

        if ($this->id) {
            $subQuery = self::find()->select('id')->where(['parent_id' => $this->id]);
            $query = self::find()->alias('categories')
                ->where(['not in', 'categories.parent_id', $subQuery])
                ->andWhere(['!=', 'categories.parent_id', $this->id])
                ->orWhere(['IS', 'categories.parent_id', null])
                ->andWhere(['!=', 'categories.id', $this->id])
                ->select(['id', 'name']);

            $list = $query->asArray()->all();
        } else {
            $list = self::find()->select(['id', 'name'])->asArray()->all();
        }

        if ($allLabel)
            return ArrayHelper::merge([
                '*' => Yii::t('app/modules/organization', '-- All categories --')
            ], ArrayHelper::map($list, 'id', 'name'));
        elseif ($rootLabel)
            return ArrayHelper::merge([
                0 => Yii::t('app/modules/organization', '-- Root category --')
            ], ArrayHelper::map($list, 'id', 'name'));
        else
            return ArrayHelper::map($list, 'id', 'name');
    }


    public static function getHierarchy()
    {
        $options = [];

        $parents = self::find()->where('parent_id = 0 OR parent_id IS NULL')->all();

        foreach ($parents as $id => $p) {

            $children = self::find()->where('parent_id = :parent_id', [
                'parent_id' => $p->id
            ])->all();

            $options[$p->nameLang] = ArrayHelper::map($children, 'id', 'nameLang');
        }
        return $options;
    }


    public static function getList($parent_id = 0)
    {
        $list = [];
        $models = self::find()->andWhere(['parent_id' => $parent_id])->all();
        foreach ($models as $model) {
            $childList = self::getList($model->id);
            array_merge($list, [$model->id => $model->name, $childList]);
        }

        return $list;
    }

}
