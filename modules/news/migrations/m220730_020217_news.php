<?php

namespace modules\news\migrations;

use Yii;
use console\components\Migration;

/**
 * Class m190730_020217_news
 */
class m220730_020217_news extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%news}}', [
            'id' => $this->bigPrimaryKey(),
            'organization_id' => $this->integer(11)->null(),
            'name' => $this->string(128)->notNull(),
            'alias' => $this->string(128)->notNull(),
            'image' => $this->string(255)->null(),
            'excerpt' => $this->string(255)->null(),
            'content' => $this->text()->null(),
            'source_url' => $this->string(255)->null(),
            'lang' => $this->string(5)->null(),
            'rss' => $this->boolean()->defaultValue(true),
            'public_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('{{%idx-news-lang}}', '{{%news}}', ['lang']);
        $this->createIndex('{{%idx-news-alias}}', '{{%news}}', ['name', 'alias']);
        $this->createIndex('{{%idx-news-status}}', '{{%news}}', ['alias', 'status']);
        $this->createIndex('{{%idx-news-organization}}', '{{%news}}', ['organization_id']);

        if ($this->db->driverName === 'mysql')
            $this->createIndex('{{%idx-news-content}}','{{%news}}', ['name', 'excerpt', 'content(250)'],false);
        else
            $this->createIndex('{{%idx-news-content}}','{{%news}}', ['name', 'excerpt', 'content'],false);

        $this->createIndex('{{%idx-news-author}}','{{%news}}', ['created_by', 'updated_by'],false);

        if (class_exists('\modules\organization\models\Organization') && isset(Yii::$app->modules['organization'])) {
            $this->addForeignKey(
                'fk_news_to_organization',
                '{{%news}}',
                'organization_id',
                \modules\organization\models\Organization::tableName(),
                'id',
                'NO ACTION',
                'CASCADE'
            );
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx-news-alias}}', '{{%news}}');
        $this->dropIndex('{{%idx-news-status}}', '{{%news}}');
        $this->dropIndex('{{%idx-news-organization}}', '{{%news}}');

        if (class_exists('\modules\users\models\User') && isset(Yii::$app->modules['users'])) {
            $userTable = \modules\users\models\User::tableName();
            if (!(Yii::$app->db->getTableSchema($userTable, true) === null)) {
                $this->dropForeignKey(
                    'fk_news_to_users',
                    '{{%news}}'
                );
            }
        }

        if (class_exists('\modules\organization\models\Organization') && isset(Yii::$app->modules['organization'])) {
            $this->dropForeignKey(
                'fk_news_to_organization',
                '{{%news}}'
            );
        }

        $this->truncateTable('{{%news}}');
        $this->dropTable('{{%news}}');
    }

}
