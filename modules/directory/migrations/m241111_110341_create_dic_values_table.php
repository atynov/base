<?php
namespace modules\directory\migrations;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%dic_values}}`.
 */
class m241111_110341_create_dic_values_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('dic_values', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->defaultValue(null),
            'type' => $this->integer()->notNull(),
            'name' => 'JSONB NOT NULL',
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Create index for parent_id
        $this->createIndex(
            'idx-dic_values-parent_id',
            'dic_values',
            'parent_id'
        );

        // Add foreign key for parent_id
        $this->addForeignKey(
            'fk-dic_values-parent_id',
            'dic_values',
            'parent_id',
            'dic_values',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-dic_values-parent_id', 'dic_values');
        $this->dropIndex('idx-dic_values-parent_id', 'dic_values');
        $this->dropTable('dic_values');
    }
}
