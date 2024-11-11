<?php

namespace modules\organization\models\form;

use modules\organization\models\Content;

use modules\organization\models\Organization;
use yii\base\Model;

class OrganizationForm extends Model
{

    public $parent_id;

    public $name;

    public $text;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id'], 'integer'],
            [['name', 'text'], 'safe'],
        ];
    }


    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $transaction = \Yii::$app->db->beginTransaction();

        $model = new Organization();
        $model->name = $this->name;
        $model->text = $this->text;
        $model->parent_id = $this->parent_id;
        if (!$model->save()) {
            $this->addErrors($model->getErrors());
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();
        return true;
    }

}

