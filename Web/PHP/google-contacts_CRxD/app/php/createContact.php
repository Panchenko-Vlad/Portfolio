<?php

session_start();

// ЕСЛИ ПОЛЬЗОВАТЕЛЬ НАЖАЛ ДОБАВИТЬ КОНТАКТ, ПРОВЕРЯЕМ ВАЛИДАЦИЮ, ЕСЛИ ВСЁ ВЕРНО ВЫПОЛНЯЕМ ЗАПРОС НА ДОБАВЛЕНИЕ
if (isset($_GET['submit'])) {

    // ЕСЛИ У НАС НЕТ ДАННЫХ АУТЕНИТИФИКАЦИИ, ТО ОТПРАВЛЯЕМ ПОЛЬЗОВАТЕЛЯ НА СТРАНИЦУ АВТОРИЗАЦИИ
    if(!isset($_SESSION['google_code']) || !isset($_SESSION['access_token']))
        header("Location: " . $googleImportUrl);

    $firstName = $_GET['firstName'];
    $lastName = $_GET['lastName'];
    $email = $_GET['email'];
    $phone = $_GET['phone'];

    $errors = false;

    if (!checkName($firstName))
        $errors['firstName'] = 'Имя должно состоять не менее чем из двух символов';
    if (!checkName($lastName))
        $errors['lastName'] = 'Фамилия должна состоять не менее чем из двух символов';
    if (!checkPhone($phone))
        $errors['phone'] = 'Вы не верно ввели номер телефона';
    if (!checkEmail($email))
        $errors['email'] = 'Вы не верно ввели адрес электронной почты';

    if ($errors == false) {
        $contactXML = "<?xml version=\"1.0\"?> 
        <atom:entry xmlns:atom=\"http://www.w3.org/2005/Atom\"
               xmlns:gd=\"http://schemas.google.com/g/2005\"
               xmlns:gContact=\"http://schemas.google.com/contact/2008\">
             <atom:category scheme=\"http://schemas.google.com/g/2005#kind\"
               term=\"http://schemas.google.com/contact/2008#contact\"/>
             <gd:name>
                <gd:givenName>$firstName</gd:givenName>
                <gd:familyName>$lastName</gd:familyName>
                <gd:fullName>$firstName $lastName</gd:fullName>
             </gd:name>
             <atom:content type=\"text\">Notes</atom:content>
             <gd:email rel=\"http://schemas.google.com/g/2005#work\"
               primary=\"true\"
               address=\"$email\" displayName=\"$lastName $firstName[0].\"/>
             <gd:email rel=\"http://schemas.google.com/g/2005#home\"
               address=\"$email\"/>
             <gd:phoneNumber rel=\"http://schemas.google.com/g/2005#home\">$phone</gd:phoneNumber>
             <gd:im address=\"$email\"
               protocol=\"http://schemas.google.com/g/2005#GOOGLE_TALK\"
               primary=\"true\"
               rel=\"http://schemas.google.com/g/2005#home\"/>
             <gd:structuredPostalAddress
                 rel=\"http://schemas.google.com/g/2005#work\"
                 primary=\"true\">
             </gd:structuredPostalAddress>
             <gContact:groupMembershipInfo deleted=\"false\"
            href=\"http://www.google.com/m8/feeds/groups/" . $_SESSION['user_email'] . "/base/6\"/>
           </atom:entry>";

        $contactQuery = 'https://www.google.com/m8/feeds/contacts/default/full';

        $headers = [
            'Host: www.google.com',
            'Authorization: Bearer ' . $_SESSION['access_token'],
            'Content-length: ' . strlen($contactXML),
            'Content-Type: application/atom+xml; charset=UTF-8; type=feed',
            'GData-Version: 3.0'
        ];

        $userAgent = 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $contactQuery);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contactXML);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Отключаем peer, для загрузки страницы по https
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Отключаем host, для загрузки страницы по https
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400);
//        curl_setopt($ch, CURLOPT_FAILONERROR, true); // Для отображения ошибок при неудачном выполнении запроса

        $result = curl_exec($ch);

        curl_close($ch);

        header("Location: ../index.php");
    }
}

function checkName($name) {
    if (strlen($name) >= 2)
        return true;
    return false;
}

function checkPhone($phone) {
    if (strlen($phone) >= 10)
        return true;
    return false;
}

function checkEmail($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL))
        return true;
    return false;
}