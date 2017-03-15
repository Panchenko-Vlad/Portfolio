<?php

use yii\db\Migration;

/**
 * Handles the creation of table `comment`.
 */
class m170304_182119_create_comment_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('comment', [
            'id' => $this->primaryKey(),
            'text' => $this->text(),
            'user_id' => $this->integer(),
            'article_id' => $this->integer(),
            'status' => $this->smallInteger(),
            'date' => $this->date()
        ]);

        // creates index for column 'user_id'
        $this->createIndex(
            'idx-post-user_id', // Название индекса
            'comment', // Какая таблица
            'user_id' // Какое поле
        );

        // add foreign key for table 'user'
        $this->addForeignKey(
            'fk-post-user_id', // Название ключа
            'comment', // Какая таблица
            'user_id', // Какое поле
            'user', // К какой таблице
            'id', // К какому полю
            'CASCADE'
        );

        // creates index for column 'article_id'
        $this->createIndex(
            'idx-article_id',
            'comment',
            'article_id'
        );

        // add foreign key for table 'article'
        $this->addForeignKey(
            'fk-article_id',
            'comment',
            'article_id',
            'article',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('comment');
    }
}
