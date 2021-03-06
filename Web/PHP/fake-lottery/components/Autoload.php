<?php

// Функция, с помощью какой мы отлавливаем неподключенные файлы и подключаем их
spl_autoload_register(function ($class_name) {
    # List all the class directories in the array.
    $array_paths = array(
        '/models/',
        '/components/',
        '/controllers/',
        '/models/'
    );

    foreach ($array_paths as $path) {
        $path = ROOT . $path . $class_name . '.php';
        if (is_file($path)) {
            include_once $path;
        }
    }
});

// КРАТКОЕ ОПИСАНИЕ
// Зачем эта функция? Что она делает?
//
// Обычно, перед тем как использовать данные, какие находятся в другом файле, мы его подключаем, чтобы наш файл
// мог видеть эти данные и давал нам доступ к ним. В больших проектах может быть большое множество таких файлов,
// что в результате приведет к большому списку подключенных файлов. Эта же функция всё упрощает.
//
// Когда PHP не находит используемый класс в коде и видит, что данный класс не подключен к нашему файлу, он обращается
// к этой функции, куда на вход передает имя класса. После чего так как имя класса известно мы сами можем его найти и
// подключить.
//
// В этой функции мы для начала прописываем массив путей папок, где могут находится необходимые файлы. После чего
// проходим циклом и в каждом из этих путей ищем файл, какой не мог найти PHP, если он был найден, подключаем его.