<?php
namespace modules\main\models\forms;

use common\components\FormModel;
use modules\media\models\Media;
use modules\news\models\News;
use modules\organization\models\Organization;
use modules\pages\models\Pages;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\db\Expression;
use yii\db\Query;

class SearchForm extends FormModel
{
    const CATEGORY_ALL = 0;
    const CATEGORY_NEWS = 1;
    const CATEGORY_PAGE = 2;
    const CATEGORY_ORGANIZATION = 3;
    const CATEGORY_MEDIA = 4;


    public $category_id = null;

    public $q;

    public function rules()
    {
        return [
            [
                ['q'], 'string'
            ],
            [
                ['category_id'], 'integer'
            ]
        ];
    }

    public function applyFilter(&$query)
    {

        if (!empty($this->q)) {
            $query->andWhere(['OR',
                ["LIKE", "LOWER(".$query->select['name'].")", mb_strtolower($this->q, "UTF-8")],
                ["LIKE", "LOWER(".$query->select['description'].")", mb_strtolower($this->q, "UTF-8")],
            ]);
        } else {
            $query->where('0=1');
        }


//        $query->andWhere([
//            'AND', [
//                'IS NOT', $query->select['name'], NULL
//            ],
//            [
//                '!=', $query->select['name'], '{"ru-RU":"","kk-KZ":"","en-US":""}'
//            ]
//        ]);

    }


    public function search($params)
    {

//        $query = News::find();
//
        $this->load($params);
//
//        $query->andFilterWhere(['like', 'name', $this->q]);
//
//        $dataProvider = new ActiveDataProvider([
//            'query' => $query,
//            'sort'=> [
//                'defaultOrder' => [
//                    'id' => SORT_DESC
//                ]
//            ]
//        ]);
//
//        return $dataProvider;


//        if (!empty($this->search)) {
            $categories_data = self::getCategoriesData();
            if (!isset($categories_data[$this->category_id])) {
                $queries = [];
                foreach ($categories_data as $category_id => $cdata) {
                    $queries[$category_id] = new Query();
                    $queries[$category_id]->select(['main.id', 'name' => 'main.'.$cdata['name_column'], 'description' => 'main.'.$cdata['description_column'], 'ts_col' => 'main.'.$cdata['ts_column'], new Expression($category_id . ' as category')]);
                    $queries[$category_id]->from(['main' => $cdata['class']::tableName()]);
                    $queries[$category_id]->orderBy(['ts_col' => SORT_DESC]);
                    $this->applyFilter($queries[$category_id]);
                }

                $main = $queries[key($queries)];
                unset($queries[key($queries)]);
                foreach ($queries as $query) {
                    $main->union($query);
                }

                $query = (new Query())->from(['all' => $main])->orderBy(['ts_col' => SORT_DESC]);

            } else {
                $query = new Query();
                $query->select(['main.id', 'name' => 'main.'. $categories_data[$this->category_id]['name_column'], 'description' => 'main.'. $categories_data[$this->category_id]['description_column'], 'ts_col' => 'main.'. $categories_data[$this->category_id]['ts_column'], new Expression($this->category_id . ' as category')]);
                $query->from(['main' => $categories_data[$this->category_id]['class']::tableName()]);
                $query->orderBy(['ts_col' => SORT_DESC]);
                $this->applyFilter($query);
            }

//            Yii::$app->data->filter = $this->toArray();

            $provider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => new Pagination([
                    'pageSize' => 50
                ])
            ]);

            $rows = $provider->getModels();
            $models = [];
            if ($rows) {
                $categories = [];
                foreach ($rows as $row) {
                    $categories[$row['category']][] = $row['id'];
                }

                foreach ($categories as $category => $ids) {
                    $models[$category] = SearchForm::getModels($category, $ids);
                }

            }

            return [
                'provider' => $provider,
                'rows' => $rows,
                'models' => $models
            ];
//        }
    }


    public static function getCategoriesData()
    {
        return [
            static::CATEGORY_NEWS => [
                'class' => News::class,
                'description_column' => 'content',
                'ts_column' => 'created_at',
                'name_column' => 'name',
                'type_column' => 'type'
            ],
            static::CATEGORY_MEDIA => [
                'class' => Media::class,
                'description_column' => 'description',
                'ts_column' => 'created_at',
                'name_column' => 'name',
                'type_column' => 'type'
            ],
            static::CATEGORY_PAGE => [
                'class' => Pages::class,
                'description_column' => 'content',
                'ts_column' => 'created_at',
                'name_column' => 'title',
                'type_column' => 'index'
            ],
            static::CATEGORY_ORGANIZATION => [
                'class' => Organization::class,
                'description_column' => 'text',
                'ts_column' => 'created_at',
                'name_column' => 'name',
                'type_column' => 'visibility'
            ]
        ];
    }

    public static function getCategories()
    {
        return [
            static::CATEGORY_ALL => \Yii::t('app', "Все разделы"),
            static::CATEGORY_NEWS => \Yii::t('app', "Новости"),
            static::CATEGORY_MEDIA => \Yii::t('app', "Медиа"),
            static::CATEGORY_PAGE => \Yii::t('app', "Страницы"),
            static::CATEGORY_ORGANIZATION => \Yii::t('app', "Организации")
        ];
    }


    public static function getModels($category, $ids)
    {
        $models = static::getCategoriesData()[$category]['class']::find()->byOrganization()->indexBy("id")->andWhere([
            'in', 'id', $ids
        ])->all();
        return $models;
    }

}
