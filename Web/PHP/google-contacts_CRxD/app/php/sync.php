<?php

session_start();

// ЕСЛИ ПОЛЬЗОВАТЕЛЬ АВТОРИЗОВАН
if(isset($_SESSION['google_code'])) {
    $auth_code = $_SESSION['google_code'];
    $max_results = 200;

    if (isset($_SESSION['refresh_token']) && $_SESSION['refresh_token']) {

        // МАССИВ ПАРАМЕТРОВ, ДЛЯ ОБНОВЛЕНИЯ ТОКЕНА
        $fields = [
            'client_id' => urlencode($google_client_id),
            'client_secret' => urlencode($google_client_secret),
            'refresh_token' => urlencode($_SESSION['refresh_token']),
            'grant_type' => urlencode('refresh_token'),
        ];

        // ФОРМИРУЕМ ПОСТ ЗАПРОС
        $post = '';
        foreach ($fields as $key => $value) {
            $post .= $key . '=' . $value . '&';
        }
        $post = rtrim($post, '&');

        // ВЫПОЛНЯЕМ ОБНОВЛЕНИЕ ТОКЕНА И ПОЛУЧАЕМ МАССИВ НОВЫХ ЗНАЧЕНИЙ В ФОРМАТЕ JSON
        $result = curl('https://accounts.google.com/o/oauth2/token', $post);

        // ПЕРЕВОДИМ JSON В ARRAY
        $response = json_decode($result);
        // ОБНОВЛЯЕМ ТОКЕН
        $_SESSION['access_token'] = $response->access_token;

    } else {

        // МАССИВ ПАРАМЕТРОВ, ДЛЯ ПОЛУЧЕНИЯ ТОКЕНА
        $fields = [
            'code' => urlencode($auth_code),
            'client_id' => urlencode($google_client_id),
            'client_secret' => urlencode($google_client_secret),
            'redirect_uri' => urlencode($google_redirect_uri),
            'grant_type' => urlencode('authorization_code'),
        ];

        // ФОРМИРУЕМ ПОСТ ЗАПРОС
        $post = '';
        foreach ($fields as $key => $value) {
            $post .= $key . '=' . $value . '&';
        }
        $post = rtrim($post, '&');

        // ВЫПОЛНЯЕМ ЗАПРОС ДЛЯ ПОЛУЧЕНИЯ ТОКЕНА И ПОЛУЧАЕМ МАССИВ В ФОРМАТЕ JSON
        $result = curl('https://accounts.google.com/o/oauth2/token', $post);

        // ПЕРЕВОДИМ JSON В ARRAY
        $response = json_decode($result);
        // СОХРАНЯЕМ ПОЛУЧЕННЫЙ ТОКЕН
        $_SESSION['access_token'] = $response->access_token;
        // СОХРАНЯЕМ РЕФРЕШ ТОКЕН, НЕОБХОДИМЫЙ КЛЮЧ ДЛЯ ОБНОВЛЕНИЯ ACCESS_TOKEN
        $_SESSION['refresh_token'] = $response->refresh_token;
    }

    // ФОРМИРУЕМ ЗАПРОС ДЛЯ ПОЛУЧЕНИЯ ВСЕХ КОНТАКТОВ ПОЛЬЗОВАТЕЛЯ
    $url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results=' . $max_results . '&alt=json&v=3.0&oauth_token=' . $_SESSION['access_token'];
    // ВЫПОЛНЯЕМ ЗАПРОС И ПОЛУЧАЕМ ОТВЕТ В ФОРМАТЕ JSON
    $xmlResponse = curl($url);
    // ПЕРЕВОДИМ JSON В ARRAY
    $contacts = json_decode($xmlResponse, true);

    // ЗАПИСЫВАЕМ В СЕССИЮ АДРЕС ЭЛЕКТРОННОЙ ПОЧТЫ ПОЛЬЗОВАТЕЛЯ
    $_SESSION['user_email'] = $contacts['feed']['author'][0]['email']['$t'];

    // ОПЕРИРУЕМ ДАННЫМИ
    $return = array();
    if (!empty($contacts['feed']['entry'])) {
        foreach ($contacts['feed']['entry'] as $contact) {

            // ПОЛУЧАЕМ НЕОБХОДИМЫЕ НАМ ДАННЫЕ О КОНТАКТЕ
            $firstName = !empty($contact['gd$name']['gd$givenName']['$t']) ? $contact['gd$name']['gd$givenName']['$t'] : ' - ';
            $lastName = !empty($contact['gd$name']['gd$familyName']['$t']) ? $contact['gd$name']['gd$familyName']['$t'] : ' - ';
            $phone = !empty($contact['gd$phoneNumber'][0]['$t']) ? $contact['gd$phoneNumber'][0]['$t'] : ' - ';
            $email = !empty($contact['gd$email'][0]['address']) ? $contact['gd$email'][0]['address'] : ' - ';

            // ПОЛУЧАЕМ УНИКАЛЬНЫЕ ДАННЫЕ КАЖДОГО КОНТАКТА (для возможности удаления этого контакта)
            $etag = !empty($contact['gd$etag']) ? $contact['gd$etag'] : false;
            $linkWithId = !empty($contact['link']['1']['href']) ? $contact['link']['1']['href'] : false;

            // ПОЛУЧАЕМ ИДЕНТИФИКАТОР КОНТАКТА
//            $id = array_pop(explode('/', $linkWithId));

            // ПОЛУЧАЕМ ЛИЧНЫЙ ETAG КОНТАКТА
//            $etag = str_replace('"', '', $etag);
//            $etag = str_replace('.', '', $etag);

            // СОХРАНЯЕМ КАЖДЫЙ КОНТАКТ В МАССИВ
            $return[] = [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'phone' => $phone,
                'email' => $email,
                'etag' => $etag,
                'linkWithId' => $linkWithId
            ];
        }
    }

    // ПОЛУЧАЕМ МАССИВ ВСЕХ КОНТАКТОВ
    $google_contacts = $return;

    // СОРТИРУЕМ КОНТАКТЫ ПО АЛФАВИТУ
    sort($google_contacts);
}