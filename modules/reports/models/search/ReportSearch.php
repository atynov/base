<?php

namespace modules\reports\models\search;

use modules\reports\models\Report;
use modules\users\models\UserDirection;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use Yii;
use yii\helpers\ArrayHelper;

class ReportSearch extends Model
{
    public $search;
    public $direction_id;
    public $organization_id;
    public $date_from;
    public $date_to;
    public $status;

    public function rules()
    {
        return [
            [['search'], 'string'],
            [['search'], 'trim'],
            [['direction_id', 'organization_id', 'status'], 'integer'],
            [['direction_id', 'organization_id', 'status'], 'default', 'value' => null],
            [['date_from', 'date_to', 'search'], 'safe'],
        ];
    }


    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['search', 'direction_id', 'organization_id', 'date_from', 'date_to', 'status'],
        ];
    }

    public function search($params)
    {
        $query = Report::find()
            ->where(['send_status'=>1])
            ->joinWith(['direction', 'organization'])
            ->orderBy(['start_date' => SORT_DESC, 'id' => SORT_DESC]);

        $directions = UserDirection::find()->where(['user_id'=>Yii::$app->user->id])->select(['direction_id'])->column();
        if (!empty($directions)) {

            $query->innerJoin('user_direction', 'user_direction.direction_id = directions.id')
                ->andWhere(['in', 'user_direction.user_id' , Yii::$app->user->id]);
        }
        $this->load($params, '');


        // Фильтр по строке поиска
        if ($this->search) {
            $query->andWhere(['ilike', 'reports.name', $this->search]);
        }

        // Фильтр по направлению
        if ($this->direction_id) {
            $query->andWhere(['direction_id' => $this->direction_id]);
        }

        // Фильтр по организации
        if ($this->organization_id) {
            $query->andWhere(['organization_id' => $this->organization_id]);
        }

        // Фильтр по статусу
        if ($this->status !== null) {
            $query->andWhere(['status' => $this->status]);
        }

        // Фильтр по диапазону дат
        if ($this->date_from) {
            $query->andWhere(['>=', 'start_date', Yii::$app->formatter->asDate($this->date_from, 'php:Y-m-d')]);
        }
        if ($this->date_to) {
            $query->andWhere(['<=', 'end_date', Yii::$app->formatter->asDate($this->date_to, 'php:Y-m-d')]);
        }


        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
    }


    public function attributeLabels()
    {
        return [
            'search' => 'Іздеу',
            'direction_id' => 'Бағыт',
            'organization_id' => 'Ұйым',
            'date_from' => 'Басталу күні',
            'date_to' => 'Аяқталу күні',
            'status' => 'Күйі',
        ];
    }
}
