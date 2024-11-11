<?php

namespace modules\organization\migrations;

use console\components\Migration;


class m220327_161517_organization_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%organization_category}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(11)->null(),
            'name' => $this->string(128)->notNull(),
            'name_lang' => $this->jsonb(),
            'text' => $this->text(),
            'text_lang' => $this->jsonb(),
            'alias' => $this->string(128),
            'orderby' => $this->integer()->null()
        ]);

        $this->createIndex('{{%idx-organization-category-alias}}', '{{%organization_category}}', ['name', 'alias']);
        $this->createIndex('{{%idx-organization-category-parent}}', '{{%organization_category}}', ['parent_id']);

        $this->addColumn('{{%organization}}', 'category_id', $this->integer()->null()->after('parent_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx-organization-category-alias}}', '{{%organization_category}}');
        $this->dropIndex('{{%idx-organization-category-parent}}', '{{%organization_category}}');

        $this->truncateTable('{{%organization_category}}');
        $this->dropTable('{{%organization_category}}');

        $this->dropColumn('{{%organization}}', 'category_id');
    }

}
