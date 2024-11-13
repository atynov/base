<?php

namespace modules\users\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use modules\users\traits\ModuleTrait;
use modules\users\Module;

/**
 * This is the model class for table "{{%user_profile}}".
 *
 * @property int $id ID
 * @property int $user_id User
 * @property string $first_name First Name
 * @property string $last_name Last Name
 * @property string $email_gravatar Email Gravatar
 * @property int $last_visit Last Visit
 * @property int $created_at Created
 * @property int $updated_at Updated
 *
 * @property User $user
 *
 * @method touch(string) TimestampBehavior
 */
class UserProfile extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_profile}}';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'last_visit', 'created_at', 'updated_at'], 'integer'],
            [['first_name', 'last_name'], 'string', 'max' => 255],
            [['email_gravatar'], 'email'],
            [['email_gravatar'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'user_id' => Module::t('module', 'Қолданушы'),
            'first_name' => Module::t('module', 'Аты'),
            'last_name' => Module::t('module', 'Тегі'),
            'email_gravatar' => Module::t('module', 'Gravatar электрондық пошта'),
            'last_visit' => Module::t('module', 'Соңғы кіру'),
            'created_at' => Module::t('module', 'Құрылған уақыты'),
            'updated_at' => Module::t('module', 'Жаңартылған уақыты'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = date('Y-m-d H:i:s');
            }
            $this->updated_at = date('Y-m-d H:i:s');

            return true;
        }
        return false;
    }
}
