<?php
namespace modules\organization\migrations;
use yii\db\Migration;

/**
 * Handles the dropping of table `{{%organization}}`.
 */
class m241111_121928_drop_organization_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('{{%news}}');
        $this->dropTable('{{%news_category}}');
        $this->dropTable('{{%organization_category}}');
        $this->dropTable('{{%organization}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createTable('{{%organization}}', [
            'id' => $this->primaryKey(),
        ]);
    }
}
