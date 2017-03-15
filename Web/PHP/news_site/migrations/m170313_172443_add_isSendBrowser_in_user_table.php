<?php

use yii\db\Migration;

class m170313_172443_add_isSendBrowser_in_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'isSendBrowser', $this->smallInteger()->defaultValue(1));
    }

    public function down()
    {
        $this->dropColumn('user', 'isSendBrowser');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
