<?php

/**
 * Модель Admin
 * Содержит всю логику работы, связанную с админ-панелью
 */
class Admin
{
    /**
     * Логин администратора
     */
    const LOGIN = 'login';

    /**
     * Пароль администратора
     */
    const PASSWORD = 'password';

    /**
     * Метод для проверки логина
     * @param string $login Логин, какой был введен пользователем
     * @return bool
     */
    public static function checkLogin($login)
    {
        if ($login == self::LOGIN)
            return true;
        return false;
    }

    /**
     * Метод для проверки пароля
     * @param string $password Пароль, какой был введен пользователем
     * @return bool
     */
    public static function checkPassword($password)
    {
        if ($password == self::PASSWORD)
            return true;
        return false;
    }

    /**
     * Метод для проверки уникального ника
     * @param string $nick
     * @return bool
     */
    public static function checkNick($nick)
    {
        $db = Db::getConnection();

        $result = $db->prepare('SELECT count(*) as count FROM users WHERE nick=:nick');
        $result->bindParam(':nick', $nick, PDO::PARAM_STR);
        $result->execute();

        if ($result->fetch()['count'] == 0)
            return true;
        return false;
    }

    /**
     * Метод, для проверки уникального имени оружия
     * @param string $name
     * @return bool
     */
    public static function checkNameGun($name)
    {
        $db = Db::getConnection();

        $result = $db->prepare('SELECT count(*) as count FROM guns WHERE name=:name');
        $result->bindParam(':name', $name, PDO::PARAM_STR);
        $result->execute();

        if ($result->fetch()['count'] == 0)
            return true;
        return false;
    }

    /**
     * Метод, для сохранения состояния админа
     */
    public static function saveState()
    {
        $_SESSION['admin'] = 'online';
    }

    /**
     * Метод, для проверки авторизации
     */
    public static function checkLogged()
    {
        if (!isset($_SESSION['admin']))
            header("Location: /admin");
    }

    /**
     * Метод, для добавления нового пользователя в бд
     * @param array $user
     * @return bool
     */
    public static function addUser($user)
    {
        $db = Db::getConnection();

        $result = $db->prepare('INSERT INTO users (nick, photo, wins) ' .
                               'VALUES (:nick, :photo, :wins)');
        $result->bindParam(':nick', $user['nick'], PDO::PARAM_STR);
        $result->bindParam(':photo', $user['photo'], PDO::PARAM_STR);
        $result->bindParam(':wins', $user['wins'], PDO::PARAM_INT);
        return $result->execute();
    }

    /**
     * Метод, для добавления нового оружия в бд
     * @param array $gun
     * @return bool
     */
    public static function addGun($gun)
    {
        $db = Db::getConnection();

        $result = $db->prepare('INSERT INTO guns (name, price, photo) ' .
                               'VALUES (:name, :price, :photo)');
        $result->bindParam(':name', $gun['name'], PDO::PARAM_STR);
        $result->bindParam(':price', $gun['price'], PDO::PARAM_INT);
        $result->bindParam(':photo', $gun['photo'], PDO::PARAM_STR);
        return $result->execute();
    }

    /**
     * Метод, для удаления указанного пользователя
     * @param int $id Идентификатор пользователя
     * @return bool
     */
    public static function deleteUser($id)
    {
        $db = Db::getConnection();

        $result = $db->prepare('DELETE FROM users WHERE id=:id');
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }

    /**
     * Метод, для удаления указанного оружия
     * @param int $id Идентификатор оружия
     * @return bool
     */
    public static function deleteGun($id)
    {
        $db = Db::getConnection();

        $result = $db->prepare('DELETE FROM guns WHERE id=:id');
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }

    /**
     * Метод, для получения всех пользователей
     * @return array
     */
    public static function getAllUsers()
    {
        $db = Db::getConnection();

        $result = $db->query('SELECT * FROM users');
        $result->execute();

        $i = 0;
        $users = array();
        while($row = $result->fetch()) {
            $users[$i]['id'] = $row['id'];
            $users[$i]['nick'] = $row['nick'];
            $users[$i]['photo'] = $row['photo'];
            $users[$i]['wins'] = $row['wins'];
            $i++;
        }

        return $users;
    }

    /**
     * Метод, для получения всего оружия
     * @return array
     */
    public static function getAllGuns()
    {
        $db = Db::getConnection();

        $result = $db->query('SELECT * FROM guns');
        $result->execute();

        $i = 0;
        $guns = array();
        while($row = $result->fetch()) {
            $guns[$i]['id'] = $row['id'];
            $guns[$i]['name'] = $row['name'];
            $guns[$i]['price'] = $row['price'];
            $guns[$i]['photo'] = $row['photo'];
            $i++;
        }

        return $guns;
    }

    /**
     * Метод, для получения данных о конкретном пользователе
     * @param int $id Идентификатор пользователя
     * @return array
     */
    public static function getUser($id)
    {
        $db = Db::getConnection();

        $result = $db->prepare('SELECT * FROM users WHERE id=:id');
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->execute();

        $i = 0;
        $users = array();
        while($row = $result->fetch()) {
            $users[$i]['id'] = $row['id'];
            $users[$i]['nick'] = $row['nick'];
            $users[$i]['photo'] = $row['photo'];
            $users[$i]['wins'] = $row['wins'];
            $i++;
        }

        return $users;
    }

    /**
     * Метод, для получения данных о конкретном оружии
     * @param int $id Идентификатор оружия
     * @return mixed
     */
    public static function getGun($id)
    {
        $db = Db::getConnection();

        $result = $db->prepare('SELECT * FROM guns WHERE id=:id');
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->execute();

        $i = 0;
        $guns = array();
        while($row = $result->fetch()) {
            $guns[$i]['id'] = $row['id'];
            $guns[$i]['name'] = $row['name'];
            $guns[$i]['price'] = $row['price'];
            $guns[$i]['photo'] = $row['photo'];
            $i++;
        }

        return $guns;
    }
}