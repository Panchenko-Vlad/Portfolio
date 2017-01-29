<?php

session_start();

// ЕСЛИ ПОЛЬЗОВАТЕЛЬ НАЖАЛ КНОПКУ РАЗЛОГИНИТЬСЯ, УДАЛЯЕМ ВСЕ СЕКРЕТНЫЕ ДАННЫЕ ЕГО АККАУНТА
if (isset($_REQUEST['logout'])) {
    unset($_SESSION['access_token']);
    unset($_SESSION['refresh_token']);
    unset($_SESSION['google_code']);
    unset($_SESSION['user_email']);

    header("Location: index.php");
}

// cURL - БИБЛИОТЕКА, ПОЗВОЛЯЮЩАЯ ДЕЛАТЬ СЕТЕВЫЕ ЗАПРОСЫ ПО УКАЗАННЫМ ДАННЫМ
function curl($url, $post = "") {
    // Инициализируем запрос к серверу и получаем дескриптор ресурса. В дальнейшем мы используем этот дескриптор,
    // чтобы указать cURL, о каком запросе идет речь.
    $curl = curl_init();

    $userAgent = 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36';

    curl_setopt($curl, CURLOPT_URL, $url); // Указываем url запроса
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); // Данные в результате запроса будут сохраняться в переменную
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5); // Количество секунд ожидания при попытке соединения

    if ($post != "") {
        curl_setopt($curl, CURLOPT_POST, 5);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    }

    curl_setopt($curl, CURLOPT_USERAGENT, $userAgent); // Указываем User Agent
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE); // При перенаправлении на другую страницу, следуем за указателем
    curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10); // Максимально позволенное кол-во секунд для выполнения cURL-функций.
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // Отключаем peer

    $contents = curl_exec($curl); // Выполняем запрос

    curl_close($curl); // Закрываем дескриптор

    return $contents;
}

// ПЕРЕМЕННЫЕ ДЛЯ OAuth 2.0. НЕОБХОДИМО ДЛЯ СИНХРОНИЗАЦИИ С GOOGLE API.
$google_client_id = '246774804674-el0am7eqin1slv1v2ncgnm8k4quimi61.apps.googleusercontent.com'; // Идентификатор клиента OAuth 2.0
$google_client_secret = 'AoxaDWyJKEsB6FNpTC9ASJXQ'; // Секрет клиента
$google_redirect_uri = 'http://f0113896.xsph.ru/'; // Разрешенные URI перенаправления

// ПОДКЛЮЧАЕМ АВТОЗАГРУЗЧИК КЛАССОВ БИБЛИОТЕКИ GOOGLE API
require_once 'google-api-php-client/src/Google/autoload.php';

// УСТАНОВКА НОВОГО КЛИЕНТА
$client = new Google_Client(); // Необходим для создания запроса аутентификации и конфигурирования возвращаемых данных
$client->setApplicationName('Php Task');
$client->setClientid($google_client_id);
$client->setClientSecret($google_client_secret);
$client->setRedirectUri($google_redirect_uri);
$client->setAccessType('offline');
$client->setApprovalPrompt('force');
$client->setScopes([
    'https://www.google.com/m8/feeds/',
]);
$googleImportUrl = $client->createAuthUrl();

// ЕСЛИ ПО МЕТОДУ GET СОДЕРЖИТСЯ ПАРАМЕТР CODE, ЗНАЧИТ МЫ ПРОШЛИ АУТЕНТИФИКАЦИЮ
if (isset($_GET['code'])) {
    $auth_code = $_GET["code"];
    $_SESSION['google_code'] = $auth_code;
}

if (isset($_GET['submit'])) {
    $data['firstName'] = $_GET['firstName'];
    $data['lastName'] = $_GET['lastName'];
    $data['email'] = $_GET['email'];
    $data['phone'] = $_GET['phone'];
}

// ФАЙЛ ДЛЯ СИНХРОНИЗАЦИИ С GOOGLE АККАУНТОМ И ПОЛУЧЕНИЕМ ВСЕХ КОНТАКТОВ
include 'php/sync.php';
// ФАЙЛ ДЛЯ СОЗДАНИЯ НОВОГО КОНТАКТА
include 'php/createContact.php';

?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">

	<title>Тестовое задание</title>
	<meta name="description" content="">

	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<link rel="stylesheet" href="css/main.min.css">
</head>

<body>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-lg-offset-4
                            col-sm-6 col-sm-offset-3
                            col-xs-10 col-xs-offset-1">

                        <div class="contact-form">
                            <h2>
                                Добавить контакт
                                <img src="img/logo_contact.png" alt="Google contacts">
                            </h2>

                            <hr />

                            <form action="index.php?<?php echo http_build_query($data); ?>" method="get">
                                <p><span class="color_element">*</span> Имя:</p>
                                <input type="text" name="firstName" class="form-control" placeholder="" value=""/>
                                <p id="text-error"><?php if (isset($errors['firstName'])) echo $errors['firstName']; ?></p>

                                <p><span class="color_element">*</span> Фамилия:</p>
                                <input type="text" name="lastName" class="form-control" placeholder=""/>
                                <p id="text-error"><?php if (isset($errors['lastName'])) echo $errors['lastName']; ?></p>

                                <p><span class="color_element">*</span> Телефон:</p>
                                <input type="text" name="phone" class="form-control" placeholder=""/>
                                <p id="text-error"><?php if (isset($errors['phone'])) echo $errors['phone']; ?></p>

                                <p><span class="color_element">*</span> E-mail:</p>
                                <input type="email" name="email" class="form-control" placeholder="" value=""/>
                                <p id="text-error"><?php if (isset($errors['email'])) echo $errors['email']; ?></p>

                                <input type="submit" name="submit" value="Добавить" class="btn btn-success" />
                            </form>
                        </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 table">

                    <br />

                    <hr />

                    <?php if (!empty($google_contacts)): ?>
                        <table class="table-bordered table-striped table">
                            <caption>Все контакты
                                <form action="php/export.php" method="post">
                                    <input type='text' name='contacts' value='<?php echo serialize($google_contacts); ?>' style='display: none'>
                                    <input type='submit' name="export" value='Экспорт' class="btn btn-default">
                                </form>
                            </caption>
                            <tbody>
                            <tr>
                                <th>Имя</th>
                                <th>Фамилия</th>
                                <th>Телефон</th>
                                <th>E-mail</th>
                            </tr>

                            <?php foreach ($google_contacts as $contact): ?>
                                <tr>
                                    <td><?php echo $contact['firstName']; ?></td>
                                    <td><?php echo $contact['lastName']; ?></td>
                                    <td><?php echo $contact['phone']; ?></td>
                                    <td><?php echo $contact['email']; ?></td>
                                    <td class="columnDel">
                                        <form action="php/deleteContact.php" method="post">
                                            <input type='text' name='linkWithId' value='<?php echo $contact['linkWithId']; ?>' style='display: none'>
                                            <input type='text' name='etag' value='<?php echo $contact['etag']; ?>' style='display: none'>
                                            <input type='submit' name="formDelete" value='Удалить' class="btn btn-danger">
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            </tbody>
                        </table>
                    <?php else: ?>
                        <h5>
                            <a href="<?php echo $googleImportUrl; ?>">
                                <button type="button" class="btn btn-default">Синхронизация с Google</button>
                            </a>
                        </h5>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['access_token'])): ?>
                    <h5>
                        <a href='https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=<?php echo $google_redirect_uri; ?>?logout'>Выйти из аккаунта</a>
                    </h5>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

	<script src="js/scripts.min.js"></script>
</body>
</html>
