<?php

use yii\db\Migration;

/**
 * Class m241205_110914_add_send_status_to_reports
 */
class m241205_110914_add_send_status_to_reports extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241205_110914_add_send_status_to_reports cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241205_110914_add_send_status_to_reports cannot be reverted.\n";

        return false;
    }
    */
}
