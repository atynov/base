<?php

namespace common\components;

use modules\organization\models\Organization;

class ActiveQuery extends \yii\db\ActiveQuery
{
    public $alias = null;

    public function init()
    {
        $modelClass = $this->modelClass;
        $this->alias = $modelClass::tableName();

        if (in_array('status',(new $this->modelClass())->attributes())) {
            $this->andWhere(['or',
                ['is', $this->alias . '.status', null],
                ['<>', $this->alias . '.status', $modelClass::STATUS_DELETED]]);
        }

        parent::init();
    }


    public function isActive()
    {
        $modelClass = $this->modelClass;

        if (in_array('status',(new $this->modelClass())->attributes())) {
            return $this->andWhere([
                'status' => $modelClass::STATUS_ACTIVE
            ]);
        }
        return $this;
    }

//    public function notDeleted($alias = null)
//    {
//        if (in_array('status',(new $this->modelClass())->attributes())) {
//            return $this->onCondition(
//                ($alias ?: $this->alias) . '.status != ' . ActiveRecord::STATUS_DELETED
//            );
//        }
//        return $this;
//    }

    public function byOrganization($organization_id = null)
    {
        if ($organization_id === null && Organization::getCurrentOrganization() === null) {
            // AND \Yii::$app->user->can("SUPER")
            return $this;
        }

        return $this->andWhere([
            "$this->alias.organization_id" => $organization_id ?: Organization::getCurrentOrganization()->id
        ]);
    }

    /**
     * @param integer $organization_id
     * @return $this
     */
    public function byOrganizationOrNull($organization_id = null)
    {
        return $this->andWhere("$this->alias.organization_id IS NULL OR $this->alias.organization_id = :oid", [
            ':oid' => $organization_id ?: Organization::getCurrentOrganizationId()
        ]);
    }

    /**
     * @param null $organization_type
     * @return $this
     */
    public function byOrganizationTypeOrNull($organization_type = null)
    {
        return $this->andWhere("$this->alias.organization_type IS NULL OR $this->alias.organization_type = :btype", [
            ':btype' => $organization_type ?: Organization::getCurrentOrganization()->type
        ]);
    }

    /**
     * @return $this
     */
    public function byOrganizationOrExternalIds($organization_id = null, $ids = [])
    {
        return $this->andWhere([
            'OR',
            [$this->alias.".organization_id" => Organization::getCurrentOrganizationId()],
            ['in', $this->alias.'.id', $ids]
        ]);
    }
}
