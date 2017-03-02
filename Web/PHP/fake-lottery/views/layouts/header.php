<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <title>Lottery</title>
    <meta name="description" content="">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <meta property="og:image" content="path/to/image.jpg">

    <link rel="shortcut icon" href="/template/img/favicon/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="/template/img/favicon/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/template/img/favicon/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/template/img/favicon/apple-touch-icon-114x114.png">

    <link rel="stylesheet" href="/template/css/main.min.css">

    <!-- Chrome, Firefox OS and Opera -->
    <meta name="theme-color" content="#000">
    <!-- Windows Phone -->
    <meta name="msapplication-navbutton-color" content="#000">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-status-bar-style" content="#000">
</head>

<body>

    <div class="page-wrapper">
        <header>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-7 visible-lg hidden-md hidden-sm hidden-xs menu-elements">
                        <ul>
                            <li><a href="/" data-id="index">Играть<i class="fa fa-play"></i></a></li>
                            <li><a href="/settings" data-id="settings">Настройки<i class="fa fa-cog"></i></a></li>
                            <li><a href="/top" data-id="top">ТОП игроков<i class="fa fa-trophy"></i></a></li>
                            <li><a href="/history" data-id="history">История<i class="fa fa-history"></i></a></li>
                            <li><a href="/about" data-id="about">О сайте<i class="fa fa-info-circle"></i></a></li>
                        </ul>
                    </div>

                    <div class="col-xs-1 hidden-lg visible-md visible-sm visible-xs menu-navicon"><i class="fa fa-navicon"></i></div>

                    <div class="menu-block">
                        <ul>
                            <li><a href="/" data-id="index">Играть<i class="fa fa-play"></i></a></li>
                            <li><a href="/settings" data-id="settings">Настройки<i class="fa fa-cog"></i></a></li>
                            <li><a href="/top" data-id="top">ТОП игроков<i class="fa fa-trophy"></i></a></li>
                            <li><a href="/history" data-id="history">История<i class="fa fa-history"></i></a></li>
                            <li><a href="/about" data-id="about">О сайте<i class="fa fa-info-circle"></i></a></li>
                        </ul>
                    </div>

                    <div class="col-md-5 col-xs-12 info-user">
                        <?php if (isset($_SESSION['steam_logged'])): ?>
                            <img src="<?php echo $_SESSION['steam_logged']['mediumAvatar']; ?>" alt="" class="photo">

                            <div class="info">
                                <ul>
                                    <li class="user-name"><?php echo $_SESSION['steam_logged']['name']; ?></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <div class="syncSteam">
                                <a href="/components/steam_auth.php?login">
                                    <button class="btn">Войти<i class="fa fa-steam"></i></button>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php if (isset($_SESSION['steam_logged'])): ?>
                <div class="logout"><a href="/steam/logout">Выйти</a></div>
            <?php endif; ?>
        </header>