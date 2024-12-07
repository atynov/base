<?php

namespace modules\organization\models;

use common\components\ActiveRecord;
use modules\directory\enums\DicValueTypeEnum;
use modules\directory\models\DicValues;
use Yii;
use yii\helpers\ArrayHelper;

class Organization extends ActiveRecord
{
    public $imageFiles; // Определяем как массив для поддержки множественной загрузки

    public function init()
    {
        parent::init();
        if ($this->isNewRecord) {
            $this->status = 1;
        }
    }

    public static function tableName()
    {
        return 'organization';
    }

    public function rules()
    {
        return [
            [['name', 'address', 'cityId'], 'required'],
            [['name', 'description'], 'safe'], // JSONB fields
            [['status', 'type', 'cityId'], 'integer'],
            [['address'], 'string', 'max' => 255],
            ['status', 'default', 'value' => 1],
            ['type', 'default', 'value' => 1],
            [['imageFiles'], 'file', 'extensions' => 'png, jpg, jpeg', 'maxFiles' => 10],
            [['imageFiles'], 'safe'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Атауы',
            'description' => 'Сипаттамасы',
            'address' => 'Мекенжайы',
            'status' => 'Статус',
            'type' => 'Түрі',
            'cityId' => 'Елді мекен',
            'created_at' => 'Жасалған күні',
            'updated_at' => 'Жаңартылған күні',
        ];
    }


    public function getFiles()
    {
        return $this->hasMany(File::class, ['target_id' => 'id'])
            ->andWhere(['table' => 'organization']); // Adjust if necessary
    }

    public function getCity()
    {
        return $this->hasOne(DicValues::class, ['id' => 'cityId']);
    }

    /**
     * Получить список организаций на нужном языке
     * @param string $language
     * @return array
     */
    public static function getOrganizationsList($language = 'kk')
    {
        $orgs = self::find()
            ->all();

        // Преобразуем данные в массив с именем города на нужном языке
        return ArrayHelper::map($orgs, 'id', function ($city) use ($language) {
            return $city->name[$language] ?? $city->name['kk'];
        });
    }

    public static function getList($language = 'kk')
    {
        $orgs = static::find()
            ->select(['name', 'id'])
            ->indexBy('id')
            ->column();

        return ArrayHelper::map($orgs, 'id', function ($org) use ($language) {
            $org->name =  $org->name[$language] ?? $org->name['kk'];
            return $org;
        });
    }



}
