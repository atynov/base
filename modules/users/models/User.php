<?php

namespace modules\users\models;

use common\traits\ParamsTrait;
use modules\organization\models\Organization;
use modules\reports\models\Direction;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\StaleObjectException;
use yii\helpers\Html;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use modules\rbac\models\Role;
use modules\users\models\query\UserQuery;
use modules\users\traits\ModuleTrait;
use modules\users\Module;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id ID
 * @property string $username Username
 * @property string $email Email
 * @property string $auth_key Authorization Key
 * @property string $password_hash Hash Password
 * @property string $password_reset_token Password Token
 * @property string $email_confirm_token Email Confirm Token
 * @property int $created_at Created
 * @property int $updated_at Updated
 * @property int $status Status
 *
 * @property UserProfile $profile
 * @property string $statusLabelNstatusesArrayame
 * @property string $statusName
 * @property array $statusesArray
 * @property string $labelMailConfirm
 * @property string $newPassword
 * @property integer $organization_id
 * @property Organization $organization
 *
 * @method touch() TimestampBehavior
 */
class User extends BaseUser
{
    use ModuleTrait;
    use ParamsTrait;

    // Length password
    const LENGTH_STRING_PASSWORD_MIN = 2;
    const LENGTH_STRING_PASSWORD_MAX = 32;

    const SCENARIO_ADMIN_CREATE = 'adminCreate';

    /**
     * @var string
     */
    public $password;
    public $directions;

    /**
     * {@inheritdoc}
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'status' => self::STATUS_DELETED
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function rules()
    {
        return [
            ['username', 'required'],
            ['username', 'match', 'pattern' => '#^[\w\s_-]+$#i'],
            ['username', 'unique', 'targetClass' => self::class, 'message' => Module::t('module', 'This username is already taken.')],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],

            ['status', 'integer'],
            ['status', 'default', 'value' => self::STATUS_WAIT],
            ['status', 'in', 'range' => array_keys(self::getStatusesArray())],

            [['password'], 'required', 'on' => self::SCENARIO_ADMIN_CREATE],
            [['password'], 'string', 'min' => self::LENGTH_STRING_PASSWORD_MIN, 'max' => self::LENGTH_STRING_PASSWORD_MAX],

            [['active_organization_id', 'active_role'], 'safe'],

            // Добавление правила для organization_id
            ['organization_id', 'integer'],
            ['organization_id', 'exist', 'skipOnError' => true, 'targetClass' => Organization::class, 'targetAttribute' => ['organization_id' => 'id']],
        ];
    }


    public static function modelParams()
    {
        return [
            'active_organization_id',
            'active_role'
        ];
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'username' => Module::t('module', 'Телефон'),
            'email' => Module::t('module', 'Электрондық пошта'),
            'auth_key' => Module::t('module', 'Аутентификация кілті'),
            'password_hash' => Module::t('module', 'Құпиясөз хэші'),
            'password_reset_token' => Module::t('module', 'Құпиясөзді қалпына келтіру белгісі'),
            'organization_id' => Module::t('module', 'Мешіт'),
            'created_at' => Module::t('module', 'Құрылған уақыты'),
            'updated_at' => Module::t('module', 'Жаңартылған уақыты'),
            'status' => Module::t('module', 'Мәртебесі'),
            'userRoleName' => Module::t('module', 'Қолданушы рөлі'),
            'password' => Module::t('module', 'Құпиясөз')
        ];
    }


    /**
     * {@inheritdoc}
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(static::class);
    }

    /**
     * @return ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(UserProfile::class, ['user_id' => 'id']);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     * @throws Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Generates new password reset token
     * @throws Exception
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return array|null|ActiveRecord
     */
    public static function findByUsernameEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username or email
     *
     * @param string $string
     * @return array|null|ActiveRecord
     */
    public static function findByUsernameOrEmail($string)
    {
        return static::find()
            ->where(['or', ['username' => $string], ['email' => $string]])
            ->andWhere(['status' => self::STATUS_ACTIVE])
            ->one();
    }

    /**
     * @param mixed $email_confirm_token
     * @return bool|null|static
     */
    public static function findByEmailConfirmToken($email_confirm_token)
    {
        return static::findOne([
            'email_confirm_token' => $email_confirm_token,
            'status' => self::STATUS_WAIT
        ]);
    }

    /**
     * Removes email confirmation token
     */
    public function removeEmailConfirmToken()
    {
        $this->email_confirm_token = null;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getLabelMailConfirm($name = 'default')
    {
        if ($this->status === self::STATUS_WAIT) {
            return Html::tag('span', Html::tag('span', '', [
                'class' => 'glyphicon glyphicon-envelope'
            ]), ['class' => 'label label-' . $name]);
        }
        return '';
    }

    /**
     * @return bool
     */
    public function sendConfirmEmail()
    {
        return Yii::$app->mailer->compose([
            'html' => '@modules/users/mail/emailConfirm-html',
            'text' => '@modules/users/mail/emailConfirm-text'
        ], ['user' => $this])
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($this->email)
            ->setSubject(Module::t('module', 'Account activation!') . ' ' . Yii::$app->name)
            ->send();
    }

    /**
     * Set Status
     * @return int|string
     */
    public function setStatus()
    {
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                $this->status = self::STATUS_BLOCKED;
                break;
            case self::STATUS_DELETED:
                $this->status = self::STATUS_WAIT;
                break;
            default:
                $this->status = self::STATUS_ACTIVE;
        }
        return $this->status;
    }

    /**
     * @return string
     */
    public function getUserFullName()
    {
//        $fullName = Module::t('module', 'Guest');
//        if (!Yii::$app->user->isGuest) {
//            $fullName = $this->profile->first_name . ' ' . $this->profile->last_name;
//            $fullName = ($fullName !== ' ') ? $fullName : $this->username;
//        }
        return Html::encode(trim($this->username));
    }

    /**
     * @param integer|string $id
     * @return bool
     */
    public function isSuperAdmin($id = '')
    {
        $id = $id ?: $this->id;
        $authManager = Yii::$app->authManager;
        $roles = $authManager->getRolesByUser($id);
        foreach ($roles as $role) {
            if ($role->name === Role::ROLE_SUPER_ADMIN) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->status === self::STATUS_DELETED;
    }

    /**
     * @param bool $insert
     * @return bool
     * @throws Exception
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->generateAuthKey();
                $this->created_at = date('Y-m-d H:i:s');
            }
            $this->updated_at = date('Y-m-d H:i:s');
            if (!empty($this->newPassword)) {
                $this->setPassword($this->newPassword);
            }

            return true;
        }
        return false;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $profile = new UserProfile([
                'user_id' => $this->id,
                'email_gravatar' => $this->email
            ]);
            $profile->save();
        }
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function beforeDelete()
    {
        if ($this->isDeleted()) {
            $this->profile->delete();
            // Отвязываем от ролей
            $authManager = Yii::$app->getAuthManager();
            if ($authManager->getRolesByUser($this->id)) {
                $authManager->revokeAll($this->id);
            }
        }
        return parent::beforeDelete();
    }



    /**
     * Связь с моделью Organization
     * @return \common\components\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::class, ['id' => 'organization_id']);
    }

    /**
     * Связь с таблицей `directions` через `user_direction`.
     * @return \yii\db\ActiveQuery
     */
    public function getUserDirections()
    {
        return $this->hasMany(UserDirection::class, ['user_id' => 'id']);
    }

}
