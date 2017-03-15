<?php

use yii\db\Migration;

class m170308_165629_add_secret_key_in_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'secret_key', $this->string());
    }

    public function down()
    {
        $this->dropColumn('user', 'secret_key');
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
