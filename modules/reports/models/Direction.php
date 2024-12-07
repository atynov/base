<?php
namespace modules\reports\models;


use common\components\ActiveRecord;
use modules\users\models\User;
use modules\users\models\UserDirection;
use yii\helpers\ArrayHelper;

class Direction extends ActiveRecord
{
    public static function tableName()
    {
        return 'directions';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'safe'],
        ];
    }

    /**
     * Связь с таблицей `user_direction`.
     * @return \yii\db\ActiveQuery
     */
    public function getUserDirections()
    {
        return $this->hasMany(UserDirection::class, ['direction_id' => 'id']);
    }

    /**
     * Связь с таблицей `user` через `user_direction`.
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->via('userDirections');
    }

    public static function getList($language = 'kk')
    {
        $orgs = self::find()
            ->all();
        return ArrayHelper::map($orgs, 'id', function ($city) use ($language) {
            return $city->name[$language] ?? $city->name['kk'];
        });
    }
}