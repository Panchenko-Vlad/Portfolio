<?php

/**
 * Модель Timer
 * Содержит всю логику работы, связанную с таймером
 */
class Timer
{
    // ВОЗМОЖНЫЕ СОСТОЯНИЯ ТАЙМЕРА
    const TIMER_STOP     = 0; // Таймер ещё не начал отсчет
    const COUNTING_TIME  = 1; // Отсчет времени до лотереи
    const COUNTING_PAUSE = 2; // Отсчет времени паузы

    // ОБЩАЯ ПРОДОЛЖИТЕЛЬНОСТЬ ТАЙМЕРА И ПАУЗЫ В СЕКУНДАХ
    const FULL_TIME  = 180; // Общая длительность таймера до розыгрыша
    const PAUSE_TIME = 40; // Общая длительность паузы

    const RANGE_SUM_ADD_GAMERS = [10, 25]; // Диапазон кол-ва игроков, какие будут добавлены в лотерею
    const RANGE_TIME_ADD_GAMERS = [5, 25]; // Диапазон кол-ва секунд, через какое игрок будет добавлен в лотерею
    const RANGE_RANDOM_PRICE_FOR_GUNS = [20, 50]; // Диапазон рандомной суммарной цены всего оружия

    const SUM_WINNERS_IN_HISTORY = 5; // Общее кол-во последних победителей, какое будет хранится в истории

    /**
     * Метод, для получения текущего времени таймера, также отталкиваясь от его состояния
     * @return array
     */
    public static function getTime()
    {
        // Получаем данные о таймере
        $data = self::getData();

        // Получаем текущее время работы сервера в секундах
        $currentTime = $_SERVER['REQUEST_TIME'];

        switch ($data['state']) {
            case self::COUNTING_TIME:
                if ($data['end'] > $currentTime) {
                    return self::setupTime($data['end'] - $currentTime, self::COUNTING_TIME, Lottery::checkTimeAdd());
                } else {
                    $winner = Lottery::getWinner();
                    if (isset($winner) && !empty($winner)) {
                        return self::setupTime(self::PAUSE_TIME, self::COUNTING_PAUSE, Lottery::getGamersIsPlaying(), $winner);
                    }
                }

            case self::COUNTING_PAUSE:
                if ($data['end'] <= $currentTime) {
                    Lottery::deleteGamers();
                    Lottery::setupTimeAddForGamers(self::RANGE_TIME_ADD_GAMERS);
                    return self::setupTime(self::TIMER_STOP, self::TIMER_STOP, Lottery::getGamersIsPlaying());
                } else {
                    return self::setupTime($data['end'] - $currentTime, self::COUNTING_PAUSE, Lottery::getGamersIsPlaying(), Lottery::getWinner());
                }

            default:
                if (Lottery::getCountGamersInLottery()['count'] >= 2) {
                    $gamers = Lottery::checkTimeAdd();
                    if (count($gamers) >= 2) {
                        return self::setupTime(self::FULL_TIME, self::COUNTING_TIME, $gamers);
                    } else {
                        return self::setupTime(self::TIMER_STOP, self::TIMER_STOP, $gamers);
                    }
                } else {
                    Lottery::addGamersForLottery(self::RANGE_SUM_ADD_GAMERS);
                    Lottery::setupTimeAddForGamers(self::RANGE_TIME_ADD_GAMERS);
                    return self::setupTime(self::TIMER_STOP, self::TIMER_STOP, []);
                }
        }
    }

    /**
     * Метод, для обновления данных таймера и установка нового времени
     * @param int $time Кол-во секунд от текущего времени
     * @param int $state Состояние таймера
     * @param array $gamers Игроки в лотерее
     * @param string $winner Ник победителя (Опционально)
     * @return array
     */
    public static function setupTime($time, $state, $gamers, $winner = "")
    {
        $id = 1;
        $start = $_SERVER['REQUEST_TIME'];
        $end = $start + $time;

//        $sumPrice = 0;
//        foreach ($gamers as $gamer) {
//            $gamer['guns'] = json_decode($gamer['guns']);
//            $sumPrice += $gamer['guns']['price'];
//        }

        $data = [
            'time' => $end - $start,
            'start' => $start,
            'end' => $end,
            'state' => $state,
            'last_winner' => is_array($winner) ? $winner['nick'] : '',
            'gamers' => $gamers,
            'admin' => isset($_SESSION['admin']) ? true : false,
//            'sumPrice' => $sumPrice
        ];

        self::setData($id, $start, $end, $state);

        return $data;
    }

    /**
     * Метод, для получения данных о таймере
     * @return mixed
     */
    public static function getData()
    {
        $db = Db::getConnection();

        $result = $db->query('SELECT end, state FROM timer');
        $result->execute();

        return $result->fetch();
    }

    /**
     * Метод, для записи данных о таймере
     * @param int $id Идентификатор строки в бд (Всегда 1)
     * @param int $start Когда началось
     * @param int $end Когда закончится
     * @param int $state Состояние таймера
     */
    public static function setData($id, $start, $end, $state)
    {
        $db = Db::getConnection();

        $result = $db->prepare('INSERT INTO timer (id, start, end, state) VALUES(:id, :start, :end, :state) ' .
                               'ON DUPLICATE KEY UPDATE start=:start, end=:end, state=:state');
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->bindParam(':start', $start, PDO::PARAM_INT);
        $result->bindParam(':end', $end, PDO::PARAM_INT);
        $result->bindParam(':state', $state, PDO::PARAM_INT);
        $result->execute();
    }

    /**
     * Метод, для получения текущего состояния таймера
     * @return mixed
     */
    public static function getState()
    {
        $db = Db::getConnection();

        $result = $db->query('SELECT state FROM timer');
        $result->execute();

        return $result->fetch();
    }

    /**
     * Метод, для изменения состояния таймера
     * @param int $state Состояние, на какое необходимо изменить
     * @return bool
     */
    public static function setState($state)
    {
        $db = Db::getConnection();

        $result = $db->prepare('UPDATE timer SET state=:state');
        $result->bindParam(':state', $state, PDO::PARAM_INT);
        return $result->execute();
    }
}