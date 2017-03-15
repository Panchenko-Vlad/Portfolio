<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m170304_182103_create_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'email' => $this->string(),
            'password' => $this->string(),
            'isAdmin' =>  $this->smallInteger()->defaultValue(0),
            'status' => $this->smallInteger(),
            'settings_notice' => $this->smallInteger()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user');
    }
}
