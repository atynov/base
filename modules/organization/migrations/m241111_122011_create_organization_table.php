<?php
namespace modules\organization\migrations;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%organization}}`.
 */
class m241111_122011_create_organization_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('organization', [
            'id' => $this->primaryKey(),
            'name' => 'JSONB NOT NULL',
            'description' => 'JSONB',
            'address' => $this->string()->notNull(),
            'status' => $this->integer()->notNull()->defaultValue(1),
            'type' => $this->integer()->notNull()->defaultValue(1),
            'cityId' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Создание функции для обновления поля `updated_at`
        $this->execute("
            CREATE OR REPLACE FUNCTION update_updated_at_column()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Создание триггера для обновления поля `updated_at` при обновлении записи
        $this->execute("
            CREATE TRIGGER set_updated_at
            BEFORE UPDATE ON organization
            FOR EACH ROW
            EXECUTE PROCEDURE update_updated_at_column();
        ");

        // Add index for cityId
        $this->createIndex('idx-organization-cityId', 'organization', 'cityId');
    }

    public function safeDown()
    {
        // Удаление триггера и функции
        $this->execute("DROP TRIGGER IF EXISTS set_updated_at ON organization");
        $this->execute("DROP FUNCTION IF EXISTS update_updated_at_column");

        $this->dropTable('organization');
    }
}
