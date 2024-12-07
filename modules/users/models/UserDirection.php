<?php
namespace modules\users\models;

use modules\reports\models\Direction;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_direction".
 *
 * @property int $user_id
 * @property int $direction_id
 *
 * @property User $user
 * @property Direction $direction
 */
class UserDirection extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_direction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'direction_id'], 'required'],
            [['user_id', 'direction_id'], 'integer'],
            [['user_id', 'direction_id'], 'unique', 'targetAttribute' => ['user_id', 'direction_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'Пользователь',
            'direction_id' => 'Направление',
        ];
    }

    /**
     * Связь с таблицей `user`.
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Связь с таблицей `directions`.
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::class, ['id' => 'direction_id']);
    }
}