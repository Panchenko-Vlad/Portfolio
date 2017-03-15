<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article`.
 */
class m170304_181829_create_article_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'description' => $this->text(),
            'content' => $this->text(),
            'image' => $this->string(),
            'viewed' => $this->integer(),
            'status' => $this->smallInteger(),
            'date' => $this->date(),
            'category_id' => $this->integer()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article');
    }
}
