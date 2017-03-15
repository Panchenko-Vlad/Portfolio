<?php

namespace app\controllers;

use app\models\Article;
use app\models\Category;
use app\models\ChangeAccount;
use app\models\CommentForm;
use app\models\Email;
use app\models\User;
use Yii;
use yii\base\InvalidParamException;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $data = Article::getAll();

        return $this->render('index', [
            'articles' => $data['articles'],
            'pagination' => $data['pagination'],
            'popular' => Article::getPopular(),
            'recent' => Article::getRecent(),
            'categories' => Category::getAll(),
        ]);
    }

    public function actionView($id)
    {
        $article = Article::findOne($id);

        if (!Article::isGuest()) {
            if (Article::isAllowedFullNews()) {
                $article->viewedCounter();
            } else {
                User::setFlashMessage('error', 'Просмотр полных новостей запрещен!');
                return $this->redirect(['/site/index']);
            }
        } else {
            return $this->redirect(['/auth/login']);
        }

        return $this->render('single', [
            'article' => $article,
            'popular' => Article::getPopular(),
            'recent' => Article::getRecent(),
            'categories' => Category::getAll(),
            'comments' => $article->getArticleComments(),
            'commentForm' => new CommentForm()
        ]);
    }

    public function actionCategory($id)
    {
        $category = Category::findOne($id);

        $data = Category::getArticlesByCategory($id);

        return $this->render('category', [
            'category' => $category,
            'articles' => $data['articles'],
            'pagination' => $data['pagination'],
            'popular' => Article::getPopular(),
            'recent' => Article::getRecent(),
            'categories' => Category::getAll()
        ]);
    }

    public function actionComment($id)
    {
        $model = new CommentForm();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->saveComment($id)) {
                User::setFlashMessage('comment', 'Спасибо! Ваш комментарий добавлен.');
                return $this->redirect(['site/view', 'id' => $id]);
            }
        }
    }

    public function actionProfile()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            $userIsSendEmail = $post['User']['isSendEmail'];
            $userIsSendBrowser = $post['User']['isSendBrowser'];

            if (isset($userIsSendEmail))
                User::setSendEmail($userIsSendEmail);
            if (isset($userIsSendBrowser))
                User::setSendBrowser($userIsSendBrowser);

            $this->refresh();
        }

        if (!Yii::$app->user->isGuest) {
            return $this->render('profile', [
                'user' => User::findOne(Yii::$app->user->id)
            ]);
        } else {
            return $this->redirect(['/auth/login']);
        }
    }

    public function actionChangePageSize($pageSize)
    {
        User::setPageSize($pageSize);
        return $this->redirect(['site/index']);
    }

    public function actionChangePageSizeByCategory($pageSize, $category_id)
    {
        User::setPageSizeByCategory($pageSize);
        return $this->redirect(['site/category', 'id' => $category_id]);
    }
}
