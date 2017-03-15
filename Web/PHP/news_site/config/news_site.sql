-- phpMyAdmin SQL Dump
-- version 4.4.15.7
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1:3307
-- Время создания: Мар 11 2017 г., 20:45
-- Версия сервера: 5.5.50
-- Версия PHP: 7.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `news_site`
--

-- --------------------------------------------------------

--
-- Структура таблицы `article`
--

CREATE TABLE IF NOT EXISTS `article` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `content` text,
  `image` varchar(255) DEFAULT NULL,
  `viewed` int(11) DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `article`
--

INSERT INTO `article` (`id`, `title`, `description`, `content`, `image`, `viewed`, `status`, `date`, `category_id`) VALUES
(4, 'В поисках хайпа', '<p>Я тут рассказываю как искать хайп</p>\r\n', '<p><em>На самом деле я не знаю как его найти. Спасибо, что зарегистрировались.</em></p>\r\n', '60c32eca051eb782b97c15b22e796244.jpg', 16, 1, '2017-03-08', 4),
(5, 'Невероятные открытия', '<p>Просто невероятные новости.&nbsp;Вы будете в Шоке!</p>\r\n', '<p>На самом деле &quot;корова&quot;, это&nbsp;большой теленок</p>\r\n', 'f0a3b8c397f1db14ac8456a99ba99396.jpg', 23, 1, '2017-03-08', 3),
(6, 'Вечерочек', '<p><s>Как всегда только интересные факты</s></p>\r\n', '<p>Вечер обычно наступает перед ночью</p>\r\n', '5c3080d52c90e81757c984926b090bcf.jpg', 50, 1, '2017-03-08', 4),
(7, 'Заголовок', '<p><strong>Тут должно быть описание</strong></p>\r\n', '<p>А тут контент</p>\r\n', '8abaaf0b00b16720432fd45d0c74885c.jpg', 5, 1, '2017-03-09', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `category`
--

INSERT INTO `category` (`id`, `title`) VALUES
(2, 'Космос'),
(3, 'Природа'),
(4, 'Погода');

-- --------------------------------------------------------

--
-- Структура таблицы `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(11) NOT NULL,
  `text` text,
  `user_id` int(11) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `comment`
--

INSERT INTO `comment` (`id`, `text`, `user_id`, `article_id`, `status`, `date`) VALUES
(1, 'o_O', 28, 6, 1, '2017-03-11'),
(2, 'Боже!', 28, 5, 1, '2017-03-11');

-- --------------------------------------------------------

--
-- Структура таблицы `migration`
--

CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1488701321),
('m170304_181829_create_article_table', 1488701323),
('m170304_182047_create_category_table', 1488701324),
('m170304_182103_create_user_table', 1488701324),
('m170304_182119_create_comment_table', 1488701324),
('m170308_165629_add_secret_key_in_user_table', 1488992359),
('m170309_094453_update_user_table', 1489052907),
('m170309_174222_add_allowFullNews_in_user_table', 1489081561);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `isAdmin` smallint(6) DEFAULT '0',
  `status` smallint(6) DEFAULT NULL,
  `secret_key` varchar(255) DEFAULT NULL,
  `isSendEmail` smallint(6) DEFAULT '1',
  `isAllowFullNews` smallint(6) DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `isAdmin`, `status`, `secret_key`, `isSendEmail`, `isAllowFullNews`) VALUES
(25, 'No name', 'wikogide@binka.me', '$2y$13$fP/6hGscsyXpIWP/eEBeaenvqq84a36WeV1MlmQetv0zJ9Nl2YQfe', 0, 1, NULL, 0, 1),
(28, 'Yii-site', 'yii.site.dev@gmail.com', '$2y$13$MiVQhHHfbYj5Qyb/0VVIDu1sb7xqDm6rh1ED.WR.8Irtras48jDnK', 1, 1, NULL, 1, 1),
(32, 'Vlad', 'p.vlad96@gmail.com', '$2y$13$80vvNdmbaaQDpV2FX6.vxu14wV//WGgglUz/YuD/6Zb2fp2HTiWM6', 0, 1, NULL, 1, 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-post-user_id` (`user_id`),
  ADD KEY `idx-article_id` (`article_id`);

--
-- Индексы таблицы `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `article`
--
ALTER TABLE `article`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT для таблицы `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=33;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `fk-article_id` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk-post-user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
