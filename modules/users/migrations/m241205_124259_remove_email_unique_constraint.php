<?php
namespace modules\users\migrations;
use yii\db\Migration;

/**
 * Class m241205_124259_remove_email_unique_constraint
 */
class m241205_124259_remove_email_unique_constraint extends Migration
{
    public function safeUp()
    {
        // Удаляем ограничение уникальности для email
        $this->execute('ALTER TABLE "user" DROP CONSTRAINT IF EXISTS user_email_key');
        $this->execute('ALTER TABLE "user" DROP CONSTRAINT IF EXISTS user_password_reset_token_key');
    }

    public function safeDown()
    {
    }
}
