<?php

/**
 * Контроллер TimerController
 * Для работы с таймером
 */
class TimerController
{
    /**
     * Ссылка по какой получаем текущее время отсчета для таймера и его состояние
     */
    public function actionGetTime()
    {
        echo json_encode(Timer::getTime());
        return true;
    }

    /**
     * Ссылка по какой выполняем последние действия перед розыгрышем
     */
    public function actionSetupLottery()
    {
        Lottery::checkTimeAdd();
        echo json_encode(Timer::getTime());
        return true;
    }

    /**
     * Ссылка по какой получаем победителя лотереи
     */
    public function actionGetWinner()
    {
        echo json_encode(Lottery::getWinner());
        return true;
    }
}