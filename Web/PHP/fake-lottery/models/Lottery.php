<?php

/**
 * Модель Lottery
 * Содержит всю логику работы, связанную с лотереей
 */
class Lottery
{
    /**
     * Путь к фотографии по-умолчанию, в случае, если у пользователя или у оружия не будет фотографии
     */
    const DEFAULT_PHOTO = '/upload/images/no-image.jpg';

    /**
     * Метод, для получения победителя в лотерее
     * @return mixed
     */
    public static function getWinner()
    {
        $db = Db::getConnection();

        $result = $db->query('SELECT * FROM gamers_in_lottery WHERE role="winner"');
        $result->execute();

        $winner = $result->fetch();

        return !empty($winner) ? $winner : self::selectWinner();
    }

    /**
     * Метод, для выбора победителя
     * @return mixed
     */
    public static function selectWinner()
    {
        $gamers = self::getGamersIsPlaying();

        $winner = $gamers[rand(0, count($gamers) - 1)];

        self::setWinner($winner['id']);

        self::addWinnerInHistory($winner['nick']);
        self::incSumWinsForGamer($winner['id']);
        self::clearHistory();

        return $winner;
    }

    /**
     * Метод, для присвоения нового победителя в лотерее
     * @param int $id Идентификатор игрока
     * @return bool
     */
    public static function setWinner($id)
    {
        self::deleteAllWinner();

        $db = Db::getConnection();

        $result = $db->prepare('UPDATE gamers_in_lottery SET role="winner" WHERE id=:id');
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }

    /**
     * Метод, для удаления всех победителей в лотерее
     * @return bool
     */
    public static function deleteAllWinner()
    {
        $db = Db::getConnection();

        $result = $db->query('UPDATE gamers_in_lottery SET role="" WHERE role="winner"');
        return $result->execute();
    }


    /**
     * Метод, для получения всех игроков в лотерее
     * @return array
     */
    public static function getAllGamers()
    {
        $db = Db::getConnection();

        $result = $db->query('SELECT * FROM gamers_in_lottery');

        $i = 0;
        $gamers = array();
        while ($row = $result->fetch()) {
            $gamers[$i]['id'] = $row['id'];
            $gamers[$i]['nick'] = $row['nick'];
            $gamers[$i]['photo'] = $row['photo'];
            $gamers[$i]['guns'] = json_decode($row['guns']);
            $i++;
        }

        return $gamers;
    }

    /**
     * Метод, для получения кол-ва игроков в лотерее
     * @return mixed
     */
    public static function getCountGamersInLottery()
    {
        $db = Db::getConnection();

        $result = $db->query('SELECT count(*) as count FROM gamers_in_lottery');
        $result->execute();

        return $result->fetch();
    }


    /**
     * Вспомогательный метод, для определения рандомных оружий для игрока в лотерее
     * @param array $range Диапазон рандомной суммарной цены всего оружия
     * @return array
     */
    private static function getNewGuns($range)
    {
        $db = Db::getConnection();

        // ПОЛУЧАЕМ ВЕСЬ СПИСОК ОРУЖИЯ
        $result = $db->query('SELECT * FROM guns');
        $result->setFetchMode(PDO::FETCH_ASSOC);
        $result->execute();

        $i = 0;
        $guns = array();
        while ($row = $result->fetch()) {
            $guns[$i]['id'] = $row['id'];
            $guns[$i]['name'] = $row['name'];
            $guns[$i]['price'] = $row['price'];
            $guns[$i]['photo'] = !empty($row['photo']) &&
                                 file_exists($_SERVER['DOCUMENT_ROOT'] . $row['photo']) ? $row['photo'] : self::DEFAULT_PHOTO;
            $i++;
        }

        $sumPrice = rand($range[0], $range[1]);

        // ДОБАВЛЯЕМ НОВОЕ ОРУЖИЕ ДО ТЕХ ПОР, ПОКА СУММА ИХ НЕ СОСТАВИТ >= 20
        $priceGuns = 0;
        $guns_for_gamer = array();
        while($priceGuns < $sumPrice && count($guns) > 0) {
            $index = rand(0, count($guns) - 1);
            $gun = $guns[$index];
            unset($guns[$index]);
            $priceGuns += $gun['price'];
            $guns_for_gamer['guns'][] = $gun;
            $guns_for_gamer['price'] = $priceGuns;
            sort($guns);
        }

        return $guns_for_gamer;
    }

    /**
     * Метод, для удаления всех игроков в лотерее
     * @return bool
     */
    public static function deleteGamers()
    {
        $db = Db::getConnection();

        $result = $db->query('DELETE FROM gamers_in_lottery');
        return $result->execute();
    }

    /**
     * Метод, для добавления необходимого кол-ва новых игроков для новой лотереи
     * @param array $range Диапазон кол-ва игроков каких нужно добавить
     */
    public static function addGamersForLottery($range)
    {
        $db = Db::getConnection();

        // ПОЛУЧАЕМ ДАННЫЕ ИГРОКОВ, КАКИЕ НЕ УЧАВСТВУЮТ В ЛОТЕРЕЕ
//        $result = $db->query('SELECT * FROM users WHERE id NOT IN (SELECT id FROM gamers_in_lottery WHERE status="new")');
        $result = $db->query('SELECT * FROM users');
        $result->setFetchMode(PDO::FETCH_ASSOC);
        $result->execute();

        $i = 0;
        $gamers = array();
        while ($row = $result->fetch()) {
            $gamers[$i]['id'] = $row['id'];
            $gamers[$i]['nick'] = $row['nick'];
            $gamers[$i]['photo'] = !empty($row['photo']) &&
                                   file_exists($_SERVER['DOCUMENT_ROOT'] . $row['photo']) ? $row['photo'] : self::DEFAULT_PHOTO;
            $i++;
        }

        $sum = rand($range[0], $range[1]);

        $count = 0;
        $gamers_in_lottery = array();
        while ($count < $sum) {
            if (count($gamers) > 0) {
                // ПОЛУЧАЕМ РАНДОМНЫЙ ИНДЕКС В МАССИВЕ, ГДЕ ОПРЕДЕЛЯЕМ КАКОЙ ИГРОК ВОЙДЕТ В ЛОТЕРЕЮ
                $index = rand(0, count($gamers) - 1);

                // ПОЛУЧАЕМ ВСЕ ДАННЫЕ ИГРОКА, КАКОЙ ВОЙДЕТ В ЛОТЕРЕЮ
                if (isset($gamers[$index])) {
                    $gamer = $gamers[$index];
                    unset($gamers[$index]);
                }

                $gamer['guns'] = json_encode(self::getNewGuns(Timer::RANGE_RANDOM_PRICE_FOR_GUNS));

                $gamers_in_lottery[] = $gamer;
                $count++;
            } else {
                break;
            }
        }

        foreach ($gamers_in_lottery as $gamer) {
            $result = $db->prepare('INSERT INTO gamers_in_lottery (id, nick, photo, guns, is_playing) ' .
                                   'VALUES (:id, :nick, :photo, :guns, 0)');
            $result->bindParam(":id", $gamer['id'], PDO::PARAM_INT);
            $result->bindParam(":nick", $gamer['nick'], PDO::PARAM_STR);
            $result->bindParam(":photo", $gamer['photo'], PDO::PARAM_STR);
            $result->bindParam(":guns", $gamer['guns'], PDO::PARAM_STR);
            $result->execute();
        }
    }

    /**
     * Установка времени добавления для каждого игрока в лотерее
     * @param array $range Диапазон кол-ва секунд, через какое игрок будет добавлен в лотерею
     */
    public static function setupTimeAddForGamers($range)
    {
        $gamers = self::getAllGamers();

        $const = 0; // Указываем начальную задержку в секундах

        $times = array();
        for ($i = 0; $i < count($gamers); $i++) {
            $const += rand($range[0], $range[1]);
            $time = $_SERVER['REQUEST_TIME'] + $const;
            $times[] = $time;
        }

        shuffle($times); // Мешает элементы массива в случайном порядке

        $db = Db::getConnection();

        for ($i = 0; $i < count($gamers); $i++) {
            $result = $db->prepare('UPDATE gamers_in_lottery SET time_add=:time_add WHERE id=:id');
            $result->bindParam(':id', $gamers[$i]['id'], PDO::PARAM_INT);
            $result->bindParam(':time_add', $times[$i], PDO::PARAM_STR);
            $result->execute();
        }
    }

    /**
     * Метод, для добавления нового победителя в историю
     * @param string $nick
     * @return bool
     */
    public static function addWinnerInHistory($nick)
    {
        $db = Db::getConnection();

        $result = $db->prepare('INSERT INTO history_winners (nick) VALUES (:nick)');
        $result->bindParam(':nick', $nick, PDO::PARAM_STR);

        return $result->execute();
    }

    /**
     * Очистка истории победителей. Остается только указанное кол-во последних победителей
     * @return bool
     */
    public static function clearHistory()
    {
        $db = Db::getConnection();

        $sum = Timer::SUM_WINNERS_IN_HISTORY;

        $result = $db->prepare('DELETE FROM history_winners WHERE id <= (SELECT maxId FROM (SELECT max(id) as maxId FROM history_winners) as tmp) - :sum_skip');
        $result->bindParam(':sum_skip', $sum, PDO::PARAM_INT);

        return $result->execute();
    }

    /**
     * Метод, для увеличения кол-ва побед на 1 указанного пользователя
     * @param int $id Идентификатор пользователя
     * @return bool
     */
    public static function incSumWinsForGamer($id)
    {
        $db = Db::getConnection();

        $result = $db->prepare('UPDATE users SET wins=wins+1 WHERE id=:id');
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }

    /**
     * Метод, для получения истории последних победителей в лотерее
     * @param int $count Кол-во последних победителей, каких нужно вывести
     * @return array
     */
    public static function getHistoryWinners($count)
    {
        $db = Db::getConnection();

        $result = $db->prepare('SELECT * FROM history_winners ORDER BY id DESC LIMIT :count');
        $result->bindParam(':count', $count, PDO::PARAM_INT);
        $result->setFetchMode(PDO::FETCH_ASSOC);
        $result->execute();

        $i = 0;
        $winners = array();
        while ($row = $result->fetch()) {
            $winners[$i]['id'] = $row['id'];
            $winners[$i]['nick'] = $row['nick'];
            $i++;
        }

        return $winners;
    }

    /**
     * Метод, для получения лучших игроков в лотерее за всё время
     * @param int $count Кол-во топ игроков, каких нужно вывести
     * @return array
     */
    public static function getTopGamers($count)
    {
        $db = Db::getConnection();

        $result = $db->prepare('SELECT nick, wins FROM users ORDER BY wins DESC LIMIT :count');
        $result->bindParam(':count', $count, PDO::PARAM_INT);
        $result->setFetchMode(PDO::FETCH_ASSOC);
        $result->execute();

        $i = 0;
        $gamers = array();
        while ($row = $result->fetch()) {
            $gamers[$i]['nick'] = $row['nick'];
            $gamers[$i]['wins'] = $row['wins'];
            $i++;
        }

        return $gamers;
    }

    /**
     * Метод, для получения игроков, какие уже вошли в лотерею
     * @return array Игроки
     */
    public static function getGamersIsPlaying()
    {
        $db = Db::getConnection();

        $result = $db->query('SELECT * FROM gamers_in_lottery WHERE is_playing="1"');
        $result->execute();

        $i = 0;
        $gamers = array();
        while ($row = $result->fetch()) {
            $gamers[$i]['id'] = $row['id'];
            $gamers[$i]['nick'] = $row['nick'];
            $gamers[$i]['photo'] = $row['photo'];
            $gamers[$i]['guns'] = $row['guns'];
            $gamers[$i]['role'] = $row['role'];
            $gamers[$i]['time_add'] = $row['time_add'];
            $gamers[$i]['is_playing'] = $row['is_playing'];
            $i++;
        }

        return $gamers;
    }

    /**
     * Метод, для изменения состояния игрока, играет он в лотерее или нет
     * @param array $idGamer Идентификатор игрока
     * @param int $is_playing (1 - игрок учавствует в лотерее, 0 - игрок не учавствует в лотерее)
     * @return bool
     */
    public static function setIsPlaying($idGamer, $is_playing)
    {
        $db = Db::getConnection();

        $result = $db->prepare('UPDATE gamers_in_lottery SET is_playing=:is_playing WHERE id=:id');
        $result->bindParam(':id', $idGamer, PDO::PARAM_INT);
        $result->bindParam(':is_playing', $is_playing, PDO::PARAM_INT);
        return $result->execute();
    }

    /**
     * Метод, для проверки времени добавления всех игроков в лотерее.
     * @return array
     */
    public static function checkTimeAdd()
    {
        $db = Db::getConnection();

        $result = $db->query('SELECT * FROM gamers_in_lottery');
        $result->execute();

        $i = 0;
        $gamers = array();
        while ($row = $result->fetch()) {
            $gamers[$i]['id'] = $row['id'];
            $gamers[$i]['nick'] = $row['nick'];
            $gamers[$i]['photo'] = $row['photo'];
            $gamers[$i]['guns'] = $row['guns'];
            $gamers[$i]['role'] = $row['role'];
            $gamers[$i]['time_add'] = $row['time_add'];
            $gamers[$i]['is_playing'] = $row['is_playing'];
            $i++;
        }

        $gamers_in_lottery = array();

        foreach ($gamers as $gamer) {
            if ($gamer['is_playing'] == 1) {
                $gamers_in_lottery[] = $gamer;
            } else if ($gamer['time_add'] <= $_SERVER['REQUEST_TIME']) {
                self::setIsPlaying($gamer['id'], 1);
                $gamer['is_playing'] = 1;
                $gamers_in_lottery[] = $gamer;
            }
        }

        return $gamers_in_lottery;
    }
}