<?php
namespace modules\reports\migrations;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%reports}}`.
 */
class m241115_073230_create_reports_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('reports', [
            'id' => $this->primaryKey()->comment('Басты кілт'),
            'direction_id' => $this->integer()->notNull()->comment('Есеп түрінің идентификаторы (19 бағыттың біреуі)'),
            'name' => $this->string()->notNull()->comment('Шара атауы'),
            'organization_id' => $this->integer()->notNull()->comment('Өкілдік'),
            'start_date' => $this->date()->null()->comment('Басталу күні'),
            'end_date' => $this->date()->null()->comment('Аяқталу күні'),
            'people_count' => $this->integer()->null()->comment('Қамтылған адам саны'),
            'link' => $this->string()->null()->comment('Әлеуметтік желідегі немесе сайттағы сілтемесі'),
            'description' => $this->text()->null()->comment('Сипаттамасы'),
            'status' => $this->tinyInteger()->notNull()->comment('Мәртебе: 1 - орындалды, 2 - Орындалуда, 3 - орындалмады'),
            'user_id' => $this->integer()->notNull()->comment('Пайдаланушы идентификаторы'),
        ]);

        // direction_id үшін шетелдік кілт қосу (directions кестесіне сілтеме)
        $this->addForeignKey(
            'fk-reports-direction_id',
            'reports',
            'direction_id',
            'directions',
            'id',
            'CASCADE'
        );

        // organization_id үшін шетелдік кілт қосу (ұйымдар кестесіне сілтеме, егер ол бар болса)
        $this->addForeignKey(
            'fk-reports-organization_id',
            'reports',
            'organization_id',
            'organization',
            'id',
            'CASCADE'
        );

        // user_id үшін шетелдік кілт қосу (пайдаланушылар кестесіне сілтеме)
        $this->addForeignKey(
            'fk-reports-user_id',
            'reports',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-reports-direction_id', 'reports');
        $this->dropForeignKey('fk-reports-organization_id', 'reports');
        $this->dropForeignKey('fk-reports-user_id', 'reports');

        $this->dropTable('reports');
    }
}
