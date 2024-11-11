<?php

namespace modules\organization\models;

use common\components\ActiveRecord;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "{{%organization_category}}".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string $text
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 */

class OrganizationTypes extends ActiveRecord
{

    public $route;
    public $url;

    const DEFAULT_CATEGORY_ID = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%organization_type}}';
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
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['name'], 'required'],
            [['parent_id'], 'integer'],
            [['name'], 'string', 'min' => 3, 'max' => 128],
            [['name_lang', 'text_lang'], 'safe'],
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
            'keywords' => Yii::t('app/modules/organization', 'Keywords'),
            'created_at' => Yii::t('app/modules/organization', 'Created at'),
            'created_by' => Yii::t('app/modules/organization', 'Created by'),
            'updated_at' => Yii::t('app/modules/organization', 'Updated at'),
            'updated_by' => Yii::t('app/modules/organization', 'Updated by'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();

        if (is_null($this->url))
            $this->url = $this->getUrl();

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
     * @param bool $allLabel
     * @param bool $rootLabel
     * @return array
     */
    public function getParentsList($allLabel = true, $rootLabel = false)
    {

        if ($this->id) {
            $subQuery = self::find()->select('id')->where(['parent_id' => $this->id]);
            $query = self::find()->alias('types')
                ->where(['not in', 'types.parent_id', $subQuery])
                ->andWhere(['!=', 'types.parent_id', $this->id])
                ->orWhere(['IS', 'types.parent_id', null])
                ->andWhere(['!=', 'types.id', $this->id])
                ->select(['id', 'name']);

            $list = $query->asArray()->all();
        } else {
            $list = self::find()->select(['id', 'name'])->asArray()->all();
        }

        if ($allLabel)
            return ArrayHelper::merge([
                '*' => Yii::t('app/modules/organization', '-- All types --')
            ], ArrayHelper::map($list, 'id', 'name'));
        elseif ($rootLabel)
            return ArrayHelper::merge([
                0 => Yii::t('app/modules/organization', '-- Root category --')
            ], ArrayHelper::map($list, 'id', 'name'));
        else
            return ArrayHelper::map($list, 'id', 'name');
    }


}
