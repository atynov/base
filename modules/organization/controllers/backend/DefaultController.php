<?php

namespace modules\organization\controllers\backend;

use backend\controllers\BackendController;
//use conquer\select2\Select2Action;
use modules\organization\models\search\OrganizationSearch;
use modules\rbac\models\Role;
use yii\filters\AccessControl;

/**
 * Class DefaultController
 * @package modules\organization\controllers\backend
 */
class DefaultController extends BackendController
{
    public $modelClass = 'modules\organization\models\Organization';
    public $searchClass = OrganizationSearch::class;

//    public function actions()
//    {
//        return [
//            'ajax' => [
//                'class' => Select2Action::className(),
//                'dataCallback' => function ($q) {
//                    $modelClass = $this->modelClass;
//                    $query = $modelClass::find();
//                    return [
//                        'results' =>  $query->select([
//                            'id',
//                            'name as text',
//                        ])
//                            ->filterWhere(['like', 'name', $q])
//                            ->orderBY('name')
//                            ->asArray()
//                            ->all(),
//                    ];
//                }
//            ],
//        ];
//    }


    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['create', 'delete'],
                        'roles' => [
                            Role::ROLE_SUPER_ADMIN
                        ],
                        'allow' => true
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'update'],
                        'roles' => [
                            Role::ROLE_SUPER_ADMIN,
                            Role::ROLE_ADMIN
                        ],
                    ],
                ],
            ]
        ];
    }

}
