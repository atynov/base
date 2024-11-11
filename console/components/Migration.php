<?php

namespace console\components;

use common\models\User;

class Migration extends \yii\db\Migration
{

    const TYPE_NUMERIC = 'numeric';
    const TYPE_TIMESTAMPTZ = 'timestamptz';
    const TYPE_TIMESTAMP = 'timestamp';
    const TYPE_JSONB = 'jsonb';

    public function numeric($precision = null)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(self::TYPE_NUMERIC, $precision);
    }

    public function timestamptz($precision = null)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(self::TYPE_TIMESTAMPTZ, $precision);
    }

    public function jsonb()
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(self::TYPE_JSONB);
    }

    public function getDefaultFields()
    {
        return [
            'params' => $this->jsonb(),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_by' => $this->integer(11),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_by' => $this->integer(11),
        ];
    }


    public function createTable($table, $columns, $options = null)
    {
        if ($this->db->driverName === 'mysql') {
            $options .= 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        parent::createTable($table, array_merge($columns,  $this->getDefaultFields()), $options);

        $this->addDefaultForeignKeys($table);
    }


    private static function clearName($table)
    {
        return str_replace([
            '.',
            '%',
            '{',
            '}'
        ], '', $table);
    }

    private function addDefaultForeignKeys($table)
    {
        $this->addForeignKey(
            'fk_'.self::clearName($table).'_to_created_user',
            $table,
            'created_by',
            User::tableName(),
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_'.self::clearName($table).'_to_updated_user',
            $table,
            'updated_by',
            User::tableName(),
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    private function dropDefaultForeignKeys($table)
    {
        $this->dropForeignKey(
            'fk_'.self::clearName($table).'_to_created_user',
            '{{%'.$table.'}}'
        );
        $this->dropForeignKey(
            'fk_'.self::clearName($table).'_to_updated_user',
            '{{%'.$table.'}}'
        );
    }


    public function dropTable($table)
    {
        $this->dropDefaultForeignKeys($table);

        parent::dropTable($table);
    }

}
