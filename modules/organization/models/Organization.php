<?php

namespace modules\organization\models;

use common\helpers\OrganizationHelper;
use common\traits\ParamsTrait;
use modules\users\models\User;
use Yii;
use yii\console\Application;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "organization".
 *
 * @property int $id
 * @property int $parent_id
 * @property int $type
 * @property string $name
 * @property string $name_lang
 * @property string $text
 * @property string $text_lang
 * @property string $location
 * @property string $params
 * @property int $status
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 * @property int $category_id
 *
 * @property User $createdUser
 */
class Organization extends \common\components\ActiveRecord
{

    use ParamsTrait;
//
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'organization';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {

        return ArrayHelper::merge([
            [['parent_id', 'type', 'status', 'category_id'], 'integer'],
            [['text', 'location'], 'string'],
            [['name_lang', 'text_lang', 'params', 'deleted_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ], [[ self::modelParams(), 'safe' ]], parent::rules());
    }


    public static function modelParams()
    {
        return [
            'address',
            'phone',
            'fax',
            'email'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app/modules/organization', 'ID'),
            'parent_id' => \Yii::t('app/modules/organization', 'Parent ID'),
            'category_id' => \Yii::t('app/modules/organization', 'Category Id'),
            'type' => \Yii::t('app/modules/organization', 'Type'),
            'name' => \Yii::t('app/modules/organization', 'Name'),
            'name_lang' => \Yii::t('app/modules/organization', 'Name Lang'),
            'text' => \Yii::t('app/modules/organization', 'Text'),
            'text_lang' => \Yii::t('app/modules/organization', 'Text Lang'),
            'location' => \Yii::t('app/modules/organization', 'Location'),
            'params' => \Yii::t('app/modules/organization', 'Params'),
            'status' => \Yii::t('app/modules/organization', 'Status'),
            'created_at' => \Yii::t('app/modules/organization', 'Created At'),
            'created_by' => \Yii::t('app/modules/organization', 'Created User ID'),
            'updated_at' => \Yii::t('app/modules/organization', 'Updated At'),
            'updated_by' => \Yii::t('app/modules/organization', 'Updated User ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(OrganizationCategory::className(), ['id' => 'category_id']);
    }


    public static function getTypeList()
    {
        return [
            1 => Yii::t('app', 'Средняя школа'),
        ];
    }


    public static function getCategoriesList()
    {
        // return \common\helpers\ArrayHelper::map(OrganizationCategory::findAll(), 'id', 'nameLang');
        // return (new OrganizationCategory())->getParentsList(false, true);
        return OrganizationCategory::getHierarchy();
    }


    public static function getList()
    {
        if (!Yii::$app->has("organizations_list")) {
            Yii::$app->set("organizations_list", function() {
                return Organization::find()->indexBy('id')->all();
            });
        }
        return Yii::$app->get("organizations_list");
    }


    public static function setCurrentOrganization($organization = null, $id = null)
    {
        if ($organization) {
            static::$_current_organization = $organization;
        } else {
            static::$_current_organization = Organization::getList()[$id];
        }
        static::$_id = $id ?: ($organization ? $organization->id : null);
    }

    public static $_current_organization = -1;

    /**
     * @return array|int|null|Organization
     */
    public static function getCurrentOrganization()
    {
        if (Yii::$app instanceof Application)
            return null;

        if (static::$_current_organization === -1) {
            if (static::getCurrentOrganizationId()) {
                static::$_current_organization = static::findOne(static::getCurrentOrganizationId());
            } else {
                $organizations = Yii::$app->user->identity->organizations ?? [];
                if (!Yii::$app->user->isGuest AND $organizations) {
                    $currentOrgId = OrganizationHelper::getUnblockedOrganizationId($organizations);
                    static::$_current_organization = $currentOrgId ? static::findOne($currentOrgId) : null;
                } else {
                    static::$_current_organization = null;
                }

            }
        }
        return static::$_current_organization;
    }

    public static $_id = -1;
    public static function getCurrentOrganizationId()
    {
        if (static::$_id === -1) {
            $id = null;
            if (!Yii::$app->request->isConsoleRequest) {
                $id = Yii::$app->request->getHeaders()->get('X-ORGANIZATION-ID') ?: Yii::$app->request->get('oid');
                if (!$id AND Yii::$app->user->id AND !Yii::$app->user->can("SUPER") AND Yii::$app->user->identity->active_organization_id) {
                    $id = Yii::$app->user->identity->active_organization_id;
                }
            } else {
                if (isset(Yii::$app->controller->organization_id)) {
                    $id = Yii::$app->controller->organization_id;
                }
            }
            if (isset(Yii::$app->user)) {
                if (!$id AND Yii::$app->user->can("SUPER")) {
                    $id = 0;
                }
            }
            static::$_id = $id;
        }
        return static::$_id;
    }


    public static function setCurrentOrganizationByAlias($alias)
    {

        $organization = Organization::findOne([
            'alias' => $alias
        ]);

        if ($organization) {
            self::setCurrentOrganization($organization);
            return $organization;
        }
    }
}
