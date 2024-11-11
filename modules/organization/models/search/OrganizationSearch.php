<?php

namespace modules\organization\models\search;

use modules\organization\models\Organization;
use yii\data\ActiveDataProvider;
use Yii;

class OrganizationSearch extends \modules\organization\models\Organization
{

    public $keyword;
    /**
     * @param \yii\db\ActiveQuery $query
     * @return ActiveDataProvider
     */
    protected function getDataProvider($query)
    {
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 25
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_ASC],
                'attributes' => [
                    'id',
                    'name',
                    'text' => [
                        'asc' => ['text' => SORT_ASC],
                        'desc' => ['text' => SORT_DESC],
                        'default' => SORT_ASC
                    ],
                    'status',
                ]
            ],
        ]);
    }

    /**
     * @param array $params
     * @return mixed|ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    public function search($params)
    {
        $query = self::find();


        if (Yii::$app->user->identity && !Yii::$app->user->identity->isSuperAdmin() && Organization::getCurrentOrganization()) {
            $query->andWhere('id = ' . Organization::getCurrentOrganization()->id);
        }

        $dataProvider = $this->getDataProvider($query);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->keyword]);

        $dataProvider = $this->setTotalCount($query, $dataProvider);

        return $dataProvider;
    }

    /**
     * @param \yii\db\ActiveQuery $query
     * @param ActiveDataProvider $dataProvider
     * @return mixed
     */
    protected function setTotalCount($query, $dataProvider)
    {
        if (is_int($query->count())) {
            $dataProvider->pagination->totalCount = $query->count();
        }
        return $dataProvider;
    }


    public function applyFilter(&$query)
    {
        $query->andFilterWhere([
            'parent_id' => $this->parent_id
        ]);

//        parent::applyFilter($query);
    }
}
