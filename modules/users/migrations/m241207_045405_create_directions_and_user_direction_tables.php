<?php
namespace modules\users\migrations;
use yii\db\Migration;

/**
 * Class m241207_045405_create_directions_and_user_direction_tables
 */
class m241207_045405_create_directions_and_user_direction_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Создание таблицы "user_direction"
        $this->createTable('{{%user_direction}}', [
            'user_id' => $this->integer()->notNull(), // ID пользователя
            'direction_id' => $this->integer()->notNull(), // ID направления
            'PRIMARY KEY(user_id, direction_id)', // Составной первичный ключ
        ]);

        // Добавление внешнего ключа на таблицу "user" в "user_direction"
        $this->addForeignKey(
            'fk-user_direction-user',
            '{{%user_direction}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE', // Удаление пользователя удаляет записи
            'CASCADE'
        );

        // Добавление внешнего ключа на таблицу "directions" в "user_direction"
        $this->addForeignKey(
            'fk-user_direction-direction',
            '{{%user_direction}}',
            'direction_id',
            '{{%directions}}',
            'id',
            'CASCADE', // Удаление направления удаляет записи
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Удаление внешних ключей
        $this->dropForeignKey('fk-user_direction-user', '{{%user_direction}}');
        $this->dropForeignKey('fk-user_direction-direction', '{{%user_direction}}');

        // Удаление таблицы "user_direction"
        $this->dropTable('{{%user_direction}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241207_045405_create_directions_and_user_direction_tables cannot be reverted.\n";

        return false;
    }
    */
}
