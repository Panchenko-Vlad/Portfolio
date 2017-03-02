<?php

/**
 * Класс Db
 * Компонент для работы с базой данных
 */
class Db
{
    /**
     * Устанавливает соединение с базой данных
     * @return PDO
     */
    public static function getConnection()
    {
        // Получаем путь к файлу
        $paramsPath = ROOT . '/config/db_params.php';
        // Получаем массив
        $params = include($paramsPath);

        // Получаем подключение к бд
        $db = new PDO("mysql:host={$params['host']};dbname={$params['dbname']}", $params['user'], $params['password']);
        // Указываем бд, что нужно использовать указанную кодировку
        $db->exec('set names utf-8');

        return $db;
    }
}