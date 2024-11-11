<?php

namespace modules\news\models;

use Yii;
use yii\db\Expression;
use common\components\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\base\InvalidArgumentException;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "{{%news_category}}".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string $name_lang
 * @property string $alias
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 */

class NewsCategory extends ActiveRecord
{

    public $route;
    public $url;

    const DEFAULT_CATEGORY_ID = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%news_category}}';
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
            [['parent_id'], 'integer'],
            [['name', 'alias'], 'string', 'min' => 3, 'max' => 128],
            [['name', 'alias'], 'string', 'min' => 3, 'max' => 128],
            [['title', 'description', 'keywords'], 'string', 'max' => 255],
            ['alias', 'unique', 'message' => Yii::t('app/modules/news', 'Param attribute must be unique.')],
            ['alias', 'match', 'pattern' => '/^[A-Za-z0-9\-\_]+$/', 'message' => Yii::t('app/modules/news','It allowed only Latin alphabet, numbers and the «-», «_» characters.')],
            [['name_lang', 'created_at', 'updated_at'], 'safe'],
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
            'id' => Yii::t('app/modules/news', 'ID'),
            'parent_id' => Yii::t('app/modules/news', 'Parent ID'),
            'name' => Yii::t('app/modules/news', 'Name'),
            'alias' => Yii::t('app/modules/news', 'Alias'),
            'title' => Yii::t('app/modules/news', 'Title'),
            'description' => Yii::t('app/modules/news', 'Description'),
            'keywords' => Yii::t('app/modules/news', 'Keywords'),
            'created_at' => Yii::t('app/modules/news', 'Created at'),
            'created_by' => Yii::t('app/modules/news', 'Created by'),
            'updated_at' => Yii::t('app/modules/news', 'Updated at'),
            'updated_by' => Yii::t('app/modules/news', 'Updated by'),
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

        // Set default uncategorized category for news items
        News::updateAll(['status' => News::STATUS_DRAFT, 'category_id' => self::DEFAULT_CATEGORY_ID], ['category_id' => $this->id]);

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
     * Returns all news categories
     *
     * @param null $cond sampling conditions
     * @param bool $asArray flag if necessary to return as an array
     * @return array|ActiveRecord|null
     */
    public static function getAll($cond = null, $asArray = false) {
        if (!is_null($cond))
            $models = self::find()->where($cond);
        else
            $models = self::find();

        if ($asArray)
            return $models->asArray()->all();
        else
            return $models->all();

    }

    /**
     * Return the public route for categories URL
     * @return string
     */
    public function getRoute($route = null)
    {

        if (is_null($route)) {
            if (isset(Yii::$app->params["news.newsCategoriesRoute"])) {
                $route = Yii::$app->params["news.newsCategoriesRoute"];
            } else {

                if (!$module = Yii::$app->getModule('admin/news'))
                    $module = Yii::$app->getModule('news');

                $route = $module->newsCategoriesRoute;
            }
        }

        if ($this->parent_id) {
            if ($parent = self::find()->where(['id' => intval($this->parent_id)])->one())
                return $parent->getRoute($route) ."/". $parent->alias;

        }

        return $route;
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
     * Returns the URL to the view of the current news category
     *
     * @return string
     */
    public function getUrl($withScheme = true)
    {
        if ($this->url === null)
            $this->url = $this->getCategoryUrl();

        return $this->url;
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
                '*' => Yii::t('app/modules/news', '-- All categories --')
            ], ArrayHelper::map($list, 'id', 'name'));
        elseif ($rootLabel)
            return ArrayHelper::merge([
                0 => Yii::t('app/modules/news', '-- Root category --')
            ], ArrayHelper::map($list, 'id', 'name'));
        else
            return ArrayHelper::map($list, 'id', 'name');
    }


    /**
     * @return object of \yii\db\ActiveQuery
     */
    public function getNews($category_id = null, $asArray = false) {

        if (!($category_id === false) && !is_integer($category_id) && !is_string($category_id))
            $category_id = $this->id;

        $query = News::find()->alias('news')
            ->select(['news.id', 'news.name', 'news.alias', 'news.title', 'news.description'])
            ->leftJoin(['categories' => self::tableName()], 'news.category_id = categories.id');

        if (is_integer($category_id))
            $query->andWhere([
                'categories.id' => intval($category_id)
            ]);

        if ($asArray)
            return $query->asArray()->all();
        else
            return $query->all();

    }
}
