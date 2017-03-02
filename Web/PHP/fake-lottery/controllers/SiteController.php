<?php

/**
 * Контроллер SiteController
 * Для работы с базовыми страницами сайта
 */
class SiteController
{
    /**
     * Для работы со страницей "Лотерея"
     */
    public function actionIndex()
    {
        require_once ROOT . '/views/site/index.php';
        return true;
    }

    /**
     * Для работы со страницей "Настройки"
     */
    public function actionSettings()
    {
        if (!isset($_SESSION['steam_logged']))
            header("Location: /components/steam_auth.php?login");

        require_once ROOT . '/views/site/settings.php';
        return true;
    }

    /**
     * Для работы со страницей "ТОП игроков"
     */
    public function actionTop()
    {
        $gamers = Lottery::getTopGamers(5);
        require_once ROOT . '/views/site/top.php';
        return true;
    }

    /**
     * Для работы со страницей "История"
     */
    public function actionHistory()
    {
        $winners = Lottery::getHistoryWinners(5);
        require_once ROOT . '/views/site/history.php';
        return true;
    }

    /**
     * Для работы со страницей "О сайте"
     */
    public function actionAbout()
    {
        require_once ROOT . '/views/site/about.php';
        return true;
    }
}