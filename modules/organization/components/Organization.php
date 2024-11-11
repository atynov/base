<?php

namespace modules\menu\components;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class Organization extends Component
{

    protected $model;

    /**
     * Initialize the component
     */
    public function init()
    {
        parent::init();
        $this->model = new \modules\organization\models\Organization;
    }

    /**
     * Recursion to build a tree menu.
     *
     * @param array $items
     * @param int $parentId
     * @return array
     */
    private function buildTree(&$items = [], $parentId = 0) {
        $tree = [];
        foreach ($items as $item) {

            if (!isset($item['label']))
                continue;

            if (isset($item['auth'])) {
                if (boolval($item['auth']) && Yii::$app->user->isGuest)
                    continue;
            }

            if (is_object($item))
                $item = (array)$item;

            if ($item['parent'] == $parentId) {
                $children = $this->buildTree($items, $item['id']);

                $data = [
                    'label' => $item['label'],
                    'url' => ($item['url']) ? $item['url'] : '#'
                ];

                if (isset($item['title']))
                    $data['linkOptions']['title'] = $item['title'];

                if (isset($item['target']))
                    $data['linkOptions']['target'] = $item['target'];

                if ($children)
                    $data['items'] = $children;

                $tree[$item['id']] = $data;
                unset($items[$item['id']]);
            }
        }

        return $tree;
    }


    /**
     * Menu component, returns the items of the specified menu.
     *
     * @param $menuId
     * @param null $locale
     * @param bool $asTree
     * @return array|bool
     */
    public function getItems($menuId, $asTree = false, $locale = null)
    {

        if (is_null($locale) && isset(Yii::$app->language))
            $locale = Yii::$app->language;

        $items = $this->model->getItems($menuId, $locale, true, false);
        if (is_countable($items)) {

            $items = ArrayHelper::toArray($items, [
                'modules\menu\models\MenuItems' => [
                    'id',
                    'parent' => 'parent_id',
                    'label' => 'name',
                    'title',
                    'url' => 'source_url',
                    'auth' => 'only_auth',
                    'target' => function ($model) {
                        if (boolval($model->target_blank))
                            return '_blank';

                    }
                ]
            ]);

            if ($asTree)
                return $this->buildTree($items);
            else
                return $items;

        }

        return false;
    }
}

?>
