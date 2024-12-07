<?php

namespace modules\users\migrations;


use console\components\Migration;

/**
 * Class m161022_180040_create_table_user
 * @package modules\users\migrations
 */
class m161022_180040_create_table_user extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'email' => $this->string()->notNull()->unique(),
            'email_confirm_token' => $this->string(),
            'verification_token' => $this->string()
        ]);


        $this->createTable('{{%user_profile}}', [
            'id' => $this->primaryKey()->comment('ID'),
            'user_id' => $this->integer()->notNull()->comment('User'),
            'first_name' => $this->string()->comment('First Name'),
            'last_name' => $this->string()->comment('Last Name'),
            'email_gravatar' => $this->string()->unique()->comment('Email Gravatar'),
            'last_visit' => $this->integer()->comment('Last Visit'),
        ]);

        $this->createIndex('IDX_user_profile_user_id', '{{%user_profile}}', 'user_id');
        $this->addForeignKey('fk-user_profile-user', '{{%user_profile}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_profile}}');
        $this->dropTable('{{%user}}');
    }
}
