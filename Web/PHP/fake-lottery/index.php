<?php

// FRONT CONTROLLER

// 1. Обшие настройки
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// 2. Подключение файлов системы
define('ROOT', dirname(__FILE__)); // Создаем константу с путем, от диска, до последней папки файла, какой вызвал константу.
require_once(ROOT . '/components/Autoload.php'); // Автоподключение классов

$router = new Router();
$router->run();