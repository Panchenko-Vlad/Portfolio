<?php

use yii\db\Migration;

class m170309_094453_update_user_table extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('user', 'settings_notice');
        $this->addColumn('user', 'isSendEmail', $this->smallInteger()->defaultValue(1));
    }

    public function safeDown()
    {
        $this->dropColumn('user', 'isSendEmail');
        $this->addColumn('user', 'settings_notice', $this->string());
    }
}
