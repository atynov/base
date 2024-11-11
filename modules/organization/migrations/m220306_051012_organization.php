<?php

namespace modules\organization\migrations;

use console\components\Migration;

/**
 * Class m220326_051012_organization
 */
class m220306_051012_organization extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%organization}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(),
            'type' => $this->integer(),
            'alias' => $this->string(128)->notNull()->unique(),
            'name' => $this->string(255),
            'name_lang' => $this->jsonb(),
            'text' => $this->text(),
            'text_lang' => $this->jsonb(),
            'location' => $this->jsonb(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%organization}}');
    }
}
