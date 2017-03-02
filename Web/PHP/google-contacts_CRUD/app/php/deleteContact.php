<?php

session_start();

// ЕСЛИ ПОЛЬЗОВАТЕЛЬ НАЖАЛ НА КНОПКУ УДАЛИТЬ, ТО ПОЛУЧАЕМ ЛИЧНЫЕ ДАННЫЕ КОНТАКТА И ВЫПОЛНЯЕМ ЗАПРОС НА УДАЛЕНИЕ
if (isset($_POST['formUpdate'])) {

    $url = $_POST['linkWithId'];
    $etag = $_POST['etag'];

    $headers = [
        'Host: www.google.com',
        'Authorization: Bearer ' . $_SESSION['access_token'],
        'X-HTTP-Method-Override: POST',
        'If-Match: ' . $etag,
        'GData-Version: 3.0'
    ];

    $userAgent = 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, false);
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 400);

    $result = curl_exec($ch);

    curl_close($ch);

    header("Location: ../index.php");
} else {
    die('ОШИБКА!');
}
