<?php
namespace modules\users\migrations;
use yii\db\Migration;

/**
 * Class m241205_124632_remove_email1_unique_constraint
 */
class m241205_124632_remove_email1_unique_constraint extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "user_profile" DROP CONSTRAINT IF EXISTS user_profile_email_gravatar_key');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241205_124632_remove_email1_unique_constraint cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241205_124632_remove_email1_unique_constraint cannot be reverted.\n";

        return false;
    }
    */
}
