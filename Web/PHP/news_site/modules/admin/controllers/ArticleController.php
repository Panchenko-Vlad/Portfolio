<?php

namespace app\modules\admin\controllers;

use app\components\PushAll;
use app\models\Category;
use app\models\Email;
use app\models\ImageUpload;
use app\models\User;
use Yii;
use app\models\Article;
use app\models\ArticleSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Article models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ArticleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Article model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Article model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Article();

        $image = new ImageUpload();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->saveArticle()) {

                if (!empty($_FILES['ImageUpload']['name']['image'])) $model->imageLoad($image);

                User::sendBrowserNotice($model);
                Email::sendArticle($model);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', ['model' => $model, 'image' => $image]);
    }

    /**
     * Updates an existing Article model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $image = new ImageUpload();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->saveArticle()) {

                if (!empty($_FILES['ImageUpload']['name']['image'])) $model->imageLoad($image);

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', ['model' => $model, 'image' => $image]);
    }

    /**
     * Deletes an existing Article model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Article::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Загружаем изображение для конкретной новости
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionSetImage($id)
    {
        $model = new ImageUpload;

        if (Yii::$app->request->isPost) {
            $article = $this->findModel($id);

            if ($article->imageLoad($model)) {
                return $this->redirect(['view', 'id' => $article->id]);
            }
        }

        return $this->render('image', ['model' => $model]);
    }

    /**
     * Устанавливаем категорию для конкретной новости
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionSetCategory($id)
    {
        $article = $this->findModel($id);
        $selectedCategory = $article->category->id;
        $categories = Category::getAllInArray();

        if (Yii::$app->request->isPost) {
            $category = Yii::$app->request->post('category');
            if ($article->saveCategory($category)) {
                return $this->redirect(['view', 'id' => $article->id]);
            }
        }

        return $this->render('category', [
            'article' => $article,
            'selectedCategory' => $selectedCategory,
            'categories' =>  $categories
        ]);
    }

    /**
     * Действие скрытия указанной новости
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionHide($id)
    {
        $article = Article::findOne($id);
        if ($article->hidden()) {
            return $this->redirect('index');
        }
    }

    /**
     * Действие отображени указанной новости
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionShow($id)
    {
        $article = Article::findOne($id);
        if ($article->show()) {
            return $this->redirect('index');
        }
    }
}