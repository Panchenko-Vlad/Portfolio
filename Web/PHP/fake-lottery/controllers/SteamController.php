<?php

/**
 * Контроллер SteamController
 * Для работы со steam данными
 */
class SteamController
{
    /**
     * Ссылка по какой пользователь перейдет в настройки steam и получить ссылку на обмен
     */
    public function actionSettings()
    {
        if (isset($_SESSION['steam_logged'])) {
            $url = $_SESSION['steam_logged']['profileURL'] . 'tradeoffers/privacy#trade_offer_access_url';
            header("Location: $url");
        } else {
            header("Location: /");
        }

        return true;
    }

    /**
     * Ссылка на обмен, какую ввел пользователь
     */
    public function actionSaveLink()
    {
        if (isset($_POST['link'])) {
            $link = $_POST['link'];

            if (filter_var($link, FILTER_VALIDATE_URL)) {
                $_SESSION['link'] = $link;
                echo 1;
            } else {
                echo 0;
            }
        }

        return true;
    }

    /**
     * Ссылка по какой пользователь сможет выйти из синхронизации steam
     */
    public function actionLogout()
    {
        // Просто удаляем все данные о пользователе и возвращаем его на главную страницу
        unset($_SESSION['steam_logged']);
        unset($_SESSION['link']);

        header("Location: /");

        return true;
    }
}