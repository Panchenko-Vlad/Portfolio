<?php

use yii\db\Migration;

class m170309_174222_add_allowFullNews_in_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'isAllowFullNews', $this->smallInteger()->defaultValue(1));
    }

    public function down()
    {
        $this->dropColumn('user', 'isAllowFullNews');
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
