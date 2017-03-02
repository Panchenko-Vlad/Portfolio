<?php

/**
 * Контроллер AdminController
 * Для работы со страницами админ-панели
 */
class AdminController
{
    /**
     * Для работы со страницей "Вход в админ-панель"
     */
    public function actionLogin()
    {
        if (isset($_SESSION['admin']) && $_SESSION['admin'] == 'online') {
            header("Location: /cabinet");
        }

        if (isset($_POST['submit'])) {
            $login = $_POST['login'];
            $password = $_POST['password'];

            $errors = false;

            if (!Admin::checkLogin($login) || !Admin::checkPassword($password)) {
                $errors[] = 'Вы ввели неверные данные!';
            } else {
                Admin::saveState();
                header("Location: /cabinet");
            }
        }

        require_once ROOT . '/views/admin/login.php';
        return true;
    }

    /**
     * Для работы со страницей "Кабинет админа"
     */
    public function actionCabinet()
    {
        Admin::checkLogged();

        require_once ROOT . '/views/admin/cabinet.php';
        return true;
    }

    /**
     * Для работы со страницей "Добавление пользователя"
     */
    public function actionAddUser()
    {
        Admin::checkLogged();

        if (isset($_POST['submit'])) {
            $nick = $_POST['nick'];
            $wins = $_POST['wins'];

            $errors = false;

            if ($nick == "")
                $errors[] = 'Ник не должен быть пустым!';
            else if (!Admin::checkNick($nick))
                $errors[] = 'Такой ник уже существует в бд!';
            if (!isset($_FILES['photo']['tmp_name']) || !is_uploaded_file($_FILES['photo']['tmp_name']))
                $errors[] = 'Изображение не загружено!';
            if(!isset($_FILES['photo']['tmp_name']) || $_FILES["photo"]["size"] > 1024*3*1024)
                $errors[] = "Картинка весит больше 3-х мб!";

            if ($errors == false) {
                $photoPath = "/upload/images/users/" . str_replace(' ', '', $_FILES["photo"]["name"]);

                move_uploaded_file($_FILES["photo"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $photoPath);

                $user = [
                    'nick' => $nick,
                    'photo' => $photoPath,
                    'wins' => isset($wins) && !empty($wins) ? $wins : 0,
                ];

                if (Admin::addUser($user)) {
                    $success = 'Пользователь добавлен!';
                } else {
                    $errors[] = 'Ошибка! Пользователь не добавлен!';
                }
            }
        }

        require_once ROOT . '/views/admin/addUser.php';
        return true;
    }

    /**
     * Для работы со страницей "Добавление оружия"
     */
    public function actionAddGun()
    {
        Admin::checkLogged();

        if (isset($_POST['submit'])) {
            $name = $_POST['name'];
            $price = $_POST['price'];

            $errors = false;

            if ($name == "")
                $errors[] = 'Имя не должен быть пустым!';
            else if (!Admin::checkNameGun($name))
                $errors[] = 'Такое имя уже существует в бд!';
            if ($price <= 0)
                $errors[] = 'Цена оружия должна быть больше нуля!';
            if (!isset($_FILES['photo']['tmp_name']) || !is_uploaded_file($_FILES['photo']['tmp_name']))
                $errors[] = 'Изображение не загружено!';
            if (!isset($_FILES['photo']['tmp_name']) || $_FILES["photo"]["size"] > 1024*3*1024)
                $errors[] = "Картинка весит больше 3-х мб!";

            if ($errors == false) {
                // Создаем путь к файлу и в имени файла удаляем пробелы
                $photoPath = "/upload/images/guns/" . str_replace(' ', '', $_FILES["photo"]["name"]);

                move_uploaded_file($_FILES["photo"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $photoPath);

                $gun = [
                    'name' => $name,
                    'photo' => $photoPath,
                    'price' => isset($price) && !empty($price) ? $price : 0,
                ];

                if (Admin::addGun($gun)) {
                    $success = 'Оружие добавлено!';
                } else {
                    $errors[] = 'Ошибка! Оружие не добавлено!';
                }
            }
        }

        require_once ROOT . '/views/admin/addGun.php';
        return true;
    }

    /**
     * Ссылка по какой выбранный пользователь будет удален из бд
     */
    public function actionDeleteUser()
    {
        $id = $_POST['id'];

        Admin::deleteUser($id);

        $user = Admin::getUser($id);

        if (file_exists($user['photo'])) {
            unlink($user['photo']);
        }

        header("Location: /admin/listUsers");

        return true;
    }

    /**
     * Ссылка по какой выбранное оружие будет удалено из бд
     */
    public function actionDeleteGun()
    {
        $id = $_POST['id'];

        Admin::deleteGun($id);

        $gun = Admin::getGun($id);

        if (file_exists($gun['photo'])) {
            unlink($gun['photo']);
        }

        header("Location: /admin/listGuns");

        return true;
    }

    /**
     * Для работы со страницей "Список пользователей"
     */
    public function actionListUsers()
    {
        Admin::checkLogged();

        $users = Admin::getAllUsers();

        require_once ROOT . '/views/admin/listUsers.php';
        return true;
    }

    /**
     * Для работы со страницей "Список оружия"
     */
    public function actionListGuns()
    {
        Admin::checkLogged();

        $guns = Admin::getAllGuns();

        require_once ROOT . '/views/admin/listGuns.php';
        return true;
    }

    /**
     * Ссылка по какой выбранный игрок станет победителем лотереи
     */
    public function actionIndicateWinner()
    {
        if ($_SESSION['admin'])
            Lottery::setWinner($_POST['id']);

        header("Location: /");

        return true;
    }

    /**
     * Ссылка по какой админ сможет разлогиниться
     */
    public function actionSignOut()
    {
        unset($_SESSION['admin']);

        header("Location: /");

        return true;
    }
}