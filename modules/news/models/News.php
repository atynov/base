<?php

namespace modules\news\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%news}}".
 *
 * @property int $id
 * @property int $organization_id
 * @property int $category_id
 * @property string $name
 * @property string $alias
 * @property string $image_src
 * @property string $excerpt
 * @property string $content
 * @property boolean $status
 * @property array $source
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 * @property string $public_at
 * @property string $source_url
 */
class News extends \common\components\ActiveRecord
{
    const STATUS_DRAFT = 0; // News post has draft
    const STATUS_PUBLISHED = 1; // News post has been published

    public $uniqueAttributes = ['alias'];

    public $route;
    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%news}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge([
            [['name', 'alias', 'content'], 'required'],
            [['name', 'alias'], 'string', 'min' => 3, 'max' => 128],
            [['excerpt', 'image', 'source_url'], 'string', 'max' => 255],
            [['file'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 1, 'extensions' => 'png, jpg'],
            [['category_id'], 'integer'],
            [['public_at'], 'safe']
        ], parent::rules());
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge([
            'id' => Yii::t('app/modules/news', 'ID'),
            'name' => Yii::t('app/modules/news', 'Name'),
            'alias' => Yii::t('app/modules/news', 'Alias'),
            'image' => Yii::t('app/modules/news', 'Image'),
            'file' => Yii::t('app/modules/news', 'Image file'),
            'excerpt' => Yii::t('app/modules/news', 'Excerpt'),
            'content' => Yii::t('app/modules/news', 'News text'),
            'status' => Yii::t('app/modules/news', 'Status'),
            'created_at' => Yii::t('app/modules/news', 'Created at'),
            'created_by' => Yii::t('app/modules/news', 'Created by'),
            'updated_at' => Yii::t('app/modules/news', 'Updated at'),
            'updated_by' => Yii::t('app/modules/news', 'Updated by'),
            'source_url' => Yii::t('app/modules/news', 'Source Url'),
            'public_at' => Yii::t('app/modules/news', 'Public at'),
        ], parent::attributeLabels());
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


    public function beforeValidate()
    {
        if (is_null($this->category_id))
            $this->category_id = NewsCategory::DEFAULT_CATEGORY_ID;

        return parent::beforeValidate();
    }

    /**
     * Return the statuses list of News items
     *
     * @return array
     */
    public function getStatusesList($allStatuses = false)
    {
        if($allStatuses)
            return [
                '*' => Yii::t('app/modules/news', 'All statuses'),
                self::STATUS_DRAFT => Yii::t('app/modules/news', 'Draft'),
                self::STATUS_PUBLISHED => Yii::t('app/modules/news', 'Published'),
            ];
        else
            return [
                self::STATUS_DRAFT => Yii::t('app/modules/news', 'Draft'),
                self::STATUS_PUBLISHED => Yii::t('app/modules/news', 'Published'),
            ];
    }

    /**
     * Build and return image path for image save
     *
     * @return string
     */
    public function getImagePath($absoluteUrl = false)
    {

        if (isset(Yii::$app->params["news.imagePath"])) {
            $imagePath = Yii::$app->params["news.imagePath"];
        } else {

            if (!$module = Yii::$app->getModule('admin/news'))
                $module = Yii::$app->getModule('news');

            $imagePath = $module->imagePath;
        }

        if ($absoluteUrl)
            return \yii\helpers\Url::to(str_replace('\\', '/', $imagePath), true);
        else
            return $imagePath;

    }

    /**
     * Processed image upload and return filename
     *
     * @param null $image
     * @return bool|string
     * @throws \yii\base\Exception
     */
    public function upload($image = null)
    {
        if (!$image)
            return false;

        $path = str_replace('backend', 'frontend', Yii::getAlias('@webroot')) . $this->getImagePath();
        if ($image) {
            // Create the folder if not exist
            if (\yii\helpers\FileHelper::createDirectory($path, $mode = 0775, $recursive = true)) {
                $fileName = $image->baseName . '.' . $image->extension;
                if ($image->saveAs($path . '/' . $fileName))
                    return $fileName;
            }
        }
        return false;
    }


    /**
     * @param bool $withScheme
     * @param bool $realUrl
     * @return string|null
     */
    public function getPostUrl($withScheme = true, $realUrl = false)
    {
        return parent::getModelUrl($withScheme, $realUrl);
    }


    /**
     * @return object of \yii\db\ActiveQuery
     */
    public function getCategories($category_id = null, $asArray = false) {

        if (!($category_id === false) && !is_integer($category_id) && !is_string($category_id))
            $category_id = $this->category_id;

        $query = NewsCategory::find();// ->alias('cats');

        if (is_integer($category_id))
            $query->andWhere([
                'id' => intval($category_id)
            ]);

        if ($asArray)
            return $query->asArray()->all();
        else
            return $query->all();

    }

    /**
     * @return array
     */
    public function getCategoriesList()
    {
        $list = [];
        if ($categories = $this->getCategories(null, true)) {
            $list = ArrayHelper::merge($list, ArrayHelper::map($categories, 'id', 'name'));
        }

        return $list;
    }

    /**
     * @return array
     */
    public function getAllCategoriesList($allCategories = false)
    {
        $list = [];
        if ($allCategories)
            $list['*'] = Yii::t('app/modules/news', 'All categories');

        if ($categories = $this->getAllCategories(null, ['id', 'name'], true)) {
            $list = ArrayHelper::merge($list, ArrayHelper::map($categories, 'id', 'name'));
        }

        return $list;
    }


    /**
     * @return object of \yii\db\ActiveQuery
     */
    public function getAllCategories($cond = null, $select = ['id', 'name'], $asArray = false)
    {
        if ($cond) {
            if ($asArray)
                return NewsCategory::find()->select($select)->where($cond)->asArray()->indexBy('id')->all();
            else
                return NewsCategory::find()->select($select)->where($cond)->all();

        } else {
            if ($asArray)
                return NewsCategory::find()->select($select)->asArray()->indexBy('id')->all();
            else
                return NewsCategory::find()->select($select)->all();
        }
    }


    public function beforeSave($insert)
    {
        // Get image thumbnail
        $image = \yii\web\UploadedFile::getInstance($this, 'file');
        if ($src = $this->upload($image))
            $this->image = $src;

        return parent::beforeSave($insert);
    }

}
