<?php
namespace modules\organization\migrations;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%files}}`.
 */
class m241111_123728_create_files_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('files', [
            'id' => $this->primaryKey(),
            'url' => $this->string()->notNull(),
            'table' => $this->string()->notNull()->defaultValue('organization'),
            'target_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Добавление индекса для ускорения запросов
        $this->createIndex('idx-files-target_id', 'files', 'target_id');
    }

    public function safeDown()
    {
        $this->dropTable('files');
    }
}
