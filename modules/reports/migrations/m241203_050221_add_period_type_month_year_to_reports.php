<?php
namespace modules\reports\migrations;
use yii\db\Migration;

/**
 * Class m241203_050221_add_period_type_month_year_to_reports
 */
class m241203_050221_add_period_type_month_year_to_reports extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%reports}}', 'period_type', $this->integer()->notNull()->defaultValue(0)->comment('Тип периода'));
        $this->addColumn('{{%reports}}', 'month', $this->integer()->comment('Айы'));
        $this->addColumn('{{%reports}}', 'year', $this->integer()->notNull()->comment('Жыл'));
        $this->addColumn('{{%reports}}', 'send_status', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%reports}}', 'period_type');
        $this->dropColumn('{{%reports}}', 'month');
        $this->dropColumn('{{%reports}}', 'year');
    }
}
