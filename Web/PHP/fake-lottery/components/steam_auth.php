<?php

require 'openid.php';

$_STEAMAPI = "85230DC01766333C4EDAD4FE54949845";

session_start();

try
{
    $openid = new LightOpenID('http://f0123088.xsph.ru/components/steam_auth.php');
    if(!$openid->mode)
    {
        if(isset($_GET['login']))
        {
            $openid->identity = 'http://steamcommunity.com/openid/?l=english';
            header('Location: ' . $openid->authUrl());
        }
        ?>
        <form action="?login" method="post">
            <input type="image" src="http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_small.png">
        </form>
        <?php
    }
    elseif($openid->mode == 'cancel')
    {
        echo 'Вы отменили аутентификацию! <a href="/">Вернуться на сайт</a>';
    }
    else
    {
        if($openid->validate())
        {
            $id = $openid->identity;
            // identity is something like: http://steamcommunity.com/openid/id/76561197960435530
            // we only care about the unique account ID at the end of the URL.
            $ptn = "/^http:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
            preg_match($ptn, $id, $matches);
            echo "User is logged in (steamID: $matches[1])\n";

            $url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$_STEAMAPI&steamids=$matches[1]";
            $json_object= file_get_contents($url);
            $json_decoded = json_decode($json_object);

            foreach ($json_decoded->response->players as $player)
            {
                $_SESSION['steam_logged'] = [
                    'id' => $player->steamid,
                    'name' => $player->personaname,
                    'profileURL' => $player->profileurl,
//                    'smallAvatar' => $player->avatar,
                    'mediumAvatar' => $player->avatarmedium,
//                    'largeAvatar' => $player->avatarfull
                ];

                header("Location: /");

//                echo "
//                    <br/>Player ID: " . $_SESSION['steam_logged']['id'] . "
//                    <br/>Player Name: " . $_SESSION['steam_logged']['name'] . "
//                    <br/>Profile URL: " . $_SESSION['steam_logged']['profileURL'] . "
//                    <br/>SmallAvatar: <img src='" . $_SESSION['steam_logged']['smallAvatar'] . "'/>
//                    <br/>MediumAvatar: <img src='" . $_SESSION['steam_logged']['mediumAvatar'] . "'/>
//                    <br/>LargeAvatar: <img src='" . $_SESSION['steam_logged']['largeAvatar'] . "'/>
//                    ";
            }

        }
        else
        {
            echo "User is not logged in.\n";
        }
    }
}
catch(ErrorException $e)
{
    echo $e->getMessage();
}
?>