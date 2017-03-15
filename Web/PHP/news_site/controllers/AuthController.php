<?php

namespace app\controllers;

use app\models\ChangeAccount;
use app\models\Email;
use app\models\LoginForm;
use app\models\SignUpForm;
use app\models\User;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\Url;
use yii\web\Controller;

class AuthController extends Controller
{
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignup()
    {
        $model = new SignUpForm();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $user = $model->signUp()) {
                if ($user->status == User::ACTIVE) {
                    Yii::$app->user->login($user);
                    return $this->redirect(['site/index']);
                } else {
                    if (Email::sendActivationEmail($model->email, $user)) {
                        User::setFlashMessage('success', 'Письмо отправлено на почту. Подтвердите его пожалуйста.');
                    } else {
                        User::setFlashMessage('error', 'Ошибка. Письмо не отправлено.');
                        Yii::error('Ошибка отправки письма на почту.');
                    }
                    return $this->refresh();
                }
            }
        }

        return $this->render('signup', ['model' => $model]);
    }

    public function actionLoginVk($uid, $first_name, $last_name)
    {
        $user = new User();
        if ($user->saveFromVk($uid, $first_name, $last_name)) {
            return $this->redirect(['site/index']);
        } else {
            return $this->redirect(['auth/login']);
        }
    }

    public function actionActivateAccount($key)
    {
        try {
            $user = new ChangeAccount($key);
        } catch (InvalidParamException $e) {
            User::deleteAllSecret_key();
            User::setFlashMessage('error', 'Время действия ключа истекло или ключ недействителен.');
            return $this->redirect(Url::to(['/site/index']));
        }

        if ($user->activateAccount()) {
            $user->loginUser();
            Email::sendNewUser($user->getModelUser());
            User::setFlashMessage('success', 'Спасибо за регистрацию!');
            return $this->redirect(['/site/index']);
        } else {
            User::setFlashMessage('error', 'Ошибка активации.');
            Yii::error('Ошибка при активации.');
            return $this->redirect(['/auth/signup']);
        }
    }

    public function actionSetupLinkNewPassword($id)
    {
        $user = User::findOne($id);
        $user->generateSecretKey();
        $user->save();

        Email::sendLinkForChangePassword($user);

        User::setFlashMessage('success', 'На почту отправлена ссылка, для изменения пароля.');

        return $this->redirect(['/site/profile', 'user' => $user]);
    }

    public function actionChangePassword($key)
    {
        try {
            $user = new ChangeAccount($key);
        } catch (InvalidParamException $e) {
            User::deleteAllSecret_key();
            User::setFlashMessage('error', 'Время действия ключа истекло или ключ недействителен.');
            return $this->redirect(Url::to(['/site/index']));
        }

        if (Yii::$app->request->isPost) {
            $newPassword = Yii::$app->request->post()['User']['password'];
            if ($user->changePassword($newPassword)) {
                $user->loginUser();
                Email::sendSuccessChangePassword($user->getModelUser(), $newPassword);
                User::setFlashMessage('success', 'Пароль успешно изменен.');
                return $this->redirect('/site/index');
            } else {
                User::setFlashMessage('error', 'Ошибка при изменении пароля.');
                Yii::error('Ошибка при изменении пароля.');
                return $this->redirect('/site/index');
            }
        }

        return $this->render('change-password', ['user' => $user->getModelUser()]);
    }
}