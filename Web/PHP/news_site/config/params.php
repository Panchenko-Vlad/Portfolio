<?php

return [
    'supportEmail' => 'yii.site.news@gmail.com',
    'secretKeyExpire' => 60 * 60, // Время хранения секретного ключа (1 час)
    'emailActivation' => true, // Использовать ли подтверждение почты после регистрации или нет
    'rememberUser' => 3600 * 24 * 30,
    'default_pageSize' => 5, // Кол-во превью на главной странице
    'default_pageSizeByCategory' => 5 // Кол-во превью новостей, относящиеся к конкретной категории
];
