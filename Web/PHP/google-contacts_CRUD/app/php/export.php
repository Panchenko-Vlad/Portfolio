<?php

// ПОЛУЧАЕМ ВСЕ КОНТАКТЫ
$contacts = unserialize($_POST['contacts']);

// ЗАПОЛНЯЕМ НАЗВАНИЕ СТОЛБЦОВ
$titles = ['Имя', 'Фамилия', 'Телефон', 'E-mail'];

// ПЕРЕВОДИМ КОНТАКТЫ В ДРУГУЮ КОДИРОВКУ ДЛЯ ЧТЕНИЯ РУССКИХ СИМВОЛОВ
foreach ($contacts as $key => $contact) {
    $contact['firstName'] = toWindow($contact['firstName']);
    $contact['lastName'] = toWindow($contact['lastName']);
    $contact['email'] = toWindow($contact['email']);
    $contact['phone'] = toWindow('"' . $contact['phone'] . '"');

    // УДАЛЕНИЕ ЛИШНИХ ДАННЫХ О КОНТАКТЕ
    unset($contact['linkWithId']);
    unset($contact['etag']);

    $contacts[$key] = $contact;
}

// ПЕРЕВОДИМ НАЗВАНИЕ СТОЛБЦОВ В ДРУГУЮ КОДИРОВКУ ДЛЯ ЧТЕНИЯ РУССКИХ СИМВОЛОВ
foreach($titles as $p=>$titlesItem){
    $titles[$p] = toWindow($titlesItem);
}

// УКАЗЫВАЕМ НЕОБХОДИМЫЕ HTTP ЗАГОЛОВКИ И ИМЯ ФАЙЛА
download_send_headers('Контакты Google');

// ЗАПИСЫВАЕМ В ФАЙЛ ВСЕ КОНТАКТЫ И НАЗВАНИЯ СТОЛБЦОВ
array2csv($contacts, $titles);

/**
 * Перевод входного параметра в другую кодировку для корректного отображения русских символов
 * @param string $ii
 * @return string
 */
function toWindow($ii){
    return iconv( "utf-8", "windows-1251", $ii);
}

/**
 * Установка необходимых http заголовков и имени файла
 * @param string $filename
 */
function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Transfer-Encoding: binary");
    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=$filename.csv");
}

/**
 * Запись входных параметров в файл
 * @param array $array
 * @param array $titles
 */
function array2csv(array &$array, $titles) {
    if (count($array) != 0) {
        $df = fopen("php://output", 'w');
        fputcsv($df, $titles, ';');
        foreach ($array as $row) {
            fputcsv($df, $row, ';');
        }
        fclose($df);
    }
}

die();








