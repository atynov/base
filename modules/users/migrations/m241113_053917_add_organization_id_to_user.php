<?php
namespace modules\users\migrations;
use yii\db\Migration;

/**
 * Class m241113_053917_add_organization_id_to_user
 */
class m241113_053917_add_organization_id_to_user extends Migration
{
    public function safeUp()
    {
        // Добавляем столбец organization_id в таблицу user
        $this->addColumn('{{%user}}', 'organization_id', $this->integer()->null());

        // Добавляем внешний ключ для связи с таблицей organization
        $this->addForeignKey(
            'fk-user-organization_id',
            '{{%user}}',
            'organization_id',
            '{{%organization}}',
            'id',
            'SET NULL', // Если организация удалена, значение organization_id станет NULL
            'CASCADE'
        );

        // Добавляем индекс для ускорения запросов
        $this->createIndex(
            'idx-user-organization_id',
            '{{%user}}',
            'organization_id'
        );
    }

    public function safeDown()
    {
        // Удаляем индекс
        $this->dropIndex('idx-user-organization_id', '{{%user}}');

        // Удаляем внешний ключ
        $this->dropForeignKey('fk-user-organization_id', '{{%user}}');

        // Удаляем столбец organization_id
        $this->dropColumn('{{%user}}', 'organization_id');
    }
}
