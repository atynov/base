<?php
namespace modules\organization\models;

use yii\db\ActiveRecord;

class File extends ActiveRecord
{
    public static function tableName()
    {
        return 'files';
    }

    public function rules()
    {
        return [
            [['url', 'target_id'], 'required'],
            [['url', 'table'], 'string', 'max' => 255],
            [['target_id'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => 'URL',
            'table' => 'Таблица',
            'target_id' => 'ID записи',
            'created_at' => 'Дата создания',
        ];
    }
}