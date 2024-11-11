<?php

use common\models\User;

/**
 * Class m211210_102425_user_add
 */
class m211210_102425_user_add extends \console\components\Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $user = new User();
        $user->id = 1;
        $user->username = 'admin';
        $user->email = 'admin_advanced@advanced.com';
        $user->setPassword('password');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;

        if (!$user->save()) {
            var_dump($user->getErrors());
            return false;
        }

        $user = new User();
        $user->id = 2;
        $user->username = 'user';
        $user->email = 'user_advanced@advanced.com';
        $user->setPassword('password');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;

        if (!$user->save()) {
            var_dump($user->getErrors());
            return false;
        }

        return true;

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211210_102425_user_add cannot be reverted.\n";

        return false;
    }
}
