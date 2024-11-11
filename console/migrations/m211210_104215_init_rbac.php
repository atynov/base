<?php

use yii\db\Migration;

/**
 * Class m211210_104215_init_rbac
 */
class m211210_104215_init_rbac extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->createRole('super_admin');
        $auth->add($admin);
        $auth->assign($admin, 1);

        $user = $auth->createRole('user');
        $auth->add($user);
        $auth->addChild($admin, $user);
        $auth->assign($user, 2);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll();
    }
}
