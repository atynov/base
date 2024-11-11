<?php

namespace modules\organization\migrations;

use console\components\Migration;

/**
 * Class m230119_091616_add_cats
 */
class m230119_091616_organization_category_add_edu_cats extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->insert('{{%organization_category}}', [
            'name' => 'Education',
            'name_lang' => [
                'kk' => 'Білім',
                'ru' => 'Образование'
            ],
            'alias' => 'education',
            'status' => 1
        ]);

        $this->insert('{{%organization_category}}', [
            'name' => 'Professional Education',
            'name_lang' => [
                'kk' => 'Кәсіби білім',
                'ru' => 'Профессиональное образование'
            ],
            'alias' => 'professional_education',
            'parent_id' => 1,
            'status' => 1
        ]);

        $this->insert('{{%organization_category}}', [
            'name' => 'Secondary Education',
            'name_lang' => [
                'kk' => 'Орта білім',
                'ru' => 'Среднее образование'
            ],
            'alias' => 'secondary_education',
            'parent_id' => 1,
            'status' => 1
        ]);

        $this->insert('{{%organization_category}}', [
            'name' => 'Preschool Education',
            'name_lang' => [
                'kk' => 'Орта білім',
                'ru' => 'Дошкольное образование'
            ],
            'alias' => 'preschool_education',
            'parent_id' => 1,
            'status' => 1
        ]);

        $this->insert('{{%organization_category}}', [
            'name' => 'Additional Education',
            'name_lang' => [
                'kk' => 'Қосымша білім',
                'ru' => 'Допольнительное образование'
            ],
            'alias' => 'additional_education',
            'parent_id' => 1,
            'status' => 1
        ]);

        $this->insert('{{%organization_category}}', [
            'name' => 'Organizations for orphans and children left without parental care',
            'name_lang' => [
                'kk' => 'Жетім балалар мен ата-анасының қамқорлығынсыз қалған балаларға арналған ұйымдар',
                'ru' => 'Организации для детей-сирот и детей, оставшихся без попечения родителей'
            ],
            'alias' => 'organization_without_parent',
            'parent_id' => 1,
            'status' => 1
        ]);

        $this->insert('{{%organization_category}}', [
            'name' => 'Special Education',
            'name_lang' => [
                'kk' => 'Арнайы білім',
                'ru' => 'Специальное образование'
            ],
            'alias' => 'special_education',
            'parent_id' => 1,
            'status' => 1
        ]);

        $this->insert('{{%organization_category}}', [
            'name' => 'Other',
            'name_lang' => [
                'kk' => 'Басқа',
                'ru' => 'Прочее'
            ],
            'alias' => 'other',
            'parent_id' => 1,
            'status' => 1
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230119_091616_add_cats cannot be reverted.\n";

        return false;
    }

}
