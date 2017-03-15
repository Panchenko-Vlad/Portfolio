<?php

namespace app\models;

use Yii;
use yii\base\Model;

class CommentForm extends Model
{
    public $comment;

    public function rules()
    {
        return [
            [['comment'], 'required'],
            [['comment'], 'string', 'length' => [3, 2000]]
        ];
    }

    public function attributeLabels()
    {
        return [
            'comment' => 'Комментарий'
        ];
    }

    /**
     * Сохраняем комментарий в базу
     * @param int $article_id
     * @return bool
     */
    public function saveComment($article_id)
    {
        $comment = new Comment();
        $comment->text = $this->comment;
        $comment->status = Comment::STATUS_ALLOW;
        $comment->date = date('Y-m-d');
        $comment->user_id = Yii::$app->user->id;
        $comment->article_id = $article_id;
        return $comment->save();
    }
}