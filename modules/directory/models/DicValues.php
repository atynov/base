<?php
namespace modules\directory\models;

use modules\directory\enums\DicValueTypeEnum;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class DicValues extends ActiveRecord
{
    public $region_id;
    public $district_id;
    public static function tableName()
    {
        return 'dic_values';
    }

    public function rules()
    {
        return [
            [['type', 'name'], 'required'],
            [['parent_id', 'type', 'region_id', 'district_id'], 'integer'],
            [['name'], 'safe'], // JSONB field
        ];
    }

    public function attributeLabels()
    {
        return [
            'parent_id' => 'Родительский элемент',
            'type' => 'Тип',
            'name' => 'Наименование',
            'region_id' => 'Область',
            'district_id' => 'Район',
        ];
    }

    /**
     * Получить список городов на нужном языке
     * @param string $language
     * @return array
     */
    public static function getCitiesList($language = 'kk')
    {
        // Предполагаем, что тип города обозначен как, например, `3`
        $cities = self::find()
            ->where(['type' => DicValueTypeEnum::CITY])
            ->all();

        // Преобразуем данные в массив с именем города на нужном языке
        return ArrayHelper::map($cities, 'id', function ($city) use ($language) {
            return $city->name[$language] ?? $city->name['kk'];
        });
    }
}

