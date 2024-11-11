<?php

namespace modules\organization\migrations;

use console\components\Migration;


class m220327_171824_organization_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%organization_type}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(11)->null(),
            'name' => $this->string(128)->notNull(),
            'name_lang' => $this->jsonb(),
            'text' => $this->text(),
            'text_lang' => $this->jsonb(),
        ]);

        $this->createIndex('{{%idx-organization-types-parent}}', '{{%organization_type}}', ['parent_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx-organization-types-parent}}', '{{%organization_type}}');

        $this->truncateTable('{{%organization_type}}');
        $this->dropTable('{{%organization_type}}');
    }

}
