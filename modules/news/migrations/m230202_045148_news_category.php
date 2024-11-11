<?php

namespace modules\news\migrations;

use Yii;
use console\components\Migration;

/**
 * Class m230202_045148_news_category
 */
class m230202_045148_news_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%news_category}}', [
            'id' => $this->primaryKey(),
            'organization_id' => $this->integer(11)->null(),
            'parent_id' => $this->integer(11)->null(),
            'name' => $this->string(128)->notNull(),
            'name_lang' => $this->jsonb(),
            'alias' => $this->string(128)->notNull(),
            'title' => $this->string(255)->null(),
            'description' => $this->string(255)->null(),
            'keywords' => $this->string(255)->null(),
        ]);

        $this->createIndex('{{%idx-news-category-alias}}', '{{%news_category}}', ['name', 'alias']);
        $this->createIndex('{{%idx-news-category-parent}}', '{{%news_category}}', ['parent_id']);

        // If exist module `Users` set foreign key `created_by`, `updated_by` to `users.id`
        if (class_exists('\modules\users\models\User')) {
            $this->createIndex('{{%idx-news-category-created}}','{{%news_category}}', ['created_by'],false);
            $this->createIndex('{{%idx-news-category-updated}}','{{%news_category}}', ['updated_by'],false);
            $userTable = \modules\users\models\User::tableName();
            $this->addForeignKey(
                'fk_news_category_to_users1',
                '{{%news_category}}',
                'created_by',
                $userTable,
                'id',
                'NO ACTION',
                'CASCADE'
            );
            $this->addForeignKey(
                'fk_news_category_to_users2',
                '{{%news_category}}',
                'updated_by',
                $userTable,
                'id',
                'NO ACTION',
                'CASCADE'
            );
        }

        if (class_exists('\modules\organization\models\Organization') && isset(Yii::$app->modules['organization'])) {
            $this->addForeignKey(
                'fk_news_category_to_organization',
                '{{%news_category}}',
                'organization_id',
                \modules\organization\models\Organization::tableName(),
                'id',
                'NO ACTION',
                'CASCADE'
            );
        }

        $this->insert('{{%news_category}}', [
            'id' => 1,
            'parent_id' => 0,
            'name' => 'Uncategorized',
            'alias' => 'uncategorized',
            'title' => 'Uncategorized news'
        ]);


        $this->addColumn('{{%news}}', 'category_id', $this->integer(11)->defaultValue(1)->after('id'));

        $this->addForeignKey(
            'fk_news_to_category',
            '{{%news}}',
            'category_id',
            '{{%news_category}}',
            'id',
            'NO ACTION',
            'CASCADE'
        );

        $this->dropIndex('{{%idx-news-alias}}', '{{%news}}');
        $this->createIndex('{{%idx-news-alias}}', '{{%news}}', ['name', 'alias', 'category_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx-news-category-alias}}', '{{%news_category}}');
        $this->dropIndex('{{%idx-news-category-parent}}', '{{%news_category}}');

        if (class_exists('\modules\users\models\User')) {
            $this->dropIndex('{{%idx-news-category-created}}', '{{%news_category}}');
            $this->dropIndex('{{%idx-news-category-updated}}', '{{%news_category}}');
            $userTable = \modules\users\models\User::tableName();
            if (!(\Yii::$app->db->getTableSchema($userTable, true) === null)) {
                $this->dropForeignKey(
                    'fk_news_category_to_users1',
                    '{{%news_category}}'
                );
                $this->dropForeignKey(
                    'fk_news_category_to_users2',
                    '{{%news_category}}'
                );
            }
        }

        if (class_exists('\modules\organization\models\Organization') && isset(Yii::$app->modules['organization'])) {
            $this->dropForeignKey(
                'fk_news_category_to_organization',
                '{{%news_category}}'
            );
        }

        $this->dropColumn('{{%news}}', 'category_id');
        $this->dropForeignKey(
            'fk_news_to_category',
            '{{%news}}');

        $this->truncateTable('{{%news_category}}');
        $this->dropTable('{{%news_category}}');
    }
}
