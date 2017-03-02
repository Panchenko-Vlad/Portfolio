
var messageError = 'ERROR!';

var block = $('#winner');
var textBlock = block.find('h1');

var textSumGamers = $('.main-line-wrapper').find('.sum-gamers');

var textTimer = $('#timer');
var textMinutes = textTimer.find('.minutes');
var textSeconds = textTimer.find('.seconds');

const TIMER_STOP     = 0; // Таймер ещё не начал отсчет
const COUNTING_TIME  = 1; // Отсчет времени до лотереи
const COUNTING_PAUSE = 2; // Отсчет времени паузы

var timeInterval; // ID обновления данных таймера
var cycleUpdateData; // ID отправки и обновления данных

window.onload = function() {
    textMinutes.text('3');
    textSeconds.text('00');

    dataUpdate();
    refreshDataCycle();
};

/**
 * Цикл какой на протяжении n секунд опрашивает сервер о новых игроках и освежает время на таймере
 * @param abort boolean
 */
function refreshDataCycle(abort) {
    clearInterval(cycleUpdateData);
    if (abort == undefined || abort == false) {
        cycleUpdateData = setInterval(dataUpdate, 5000);
    }
}

/**
 * Обновление данных таймера и лотереи
 * @param isClearGamers Необходимо ли очистить данные об уже выведенных игроках
 */
function dataUpdate(isClearGamers) {
    if (isClearGamers == undefined) isClearGamers = false;

    $.ajax({
        url: '/timer/getTime',
        type: 'post',
        beforeSend: function() {
            // $('.loader').show();
        },
        complete: function() {
            // $('.loader').hide();
        },
        success: function(data) {
            data = JSON.parse(data);
            for (var i = 0; i < data['gamers'].length; i++) {
                data['gamers'][i]['guns'] = JSON.parse(data['gamers'][i]['guns']);
            }

            switch (data['state']) {
                case TIMER_STOP:
                    stateForTime();
                    if (isClearGamers == true) {
                        viewGamers([], false);
                        isClearGamers = false;
                    }
                    viewLoaderGamers(true);
                    viewGamers(data['gamers'], true, false, data['admin']);
                    break;

                case COUNTING_TIME:
                    refreshTime(data);
                    viewLoaderGamers(true);
                    viewGamers(data['gamers'], true, false, data['admin']);
                    break;

                case COUNTING_PAUSE:
                    refreshTime(data);
                    viewLoaderGamers(false);
                    viewGamers(data['gamers'], true, true, data['admin']);
                    break;
            }
        },
        error: function() {
            textBlock.text(messageError);
        }
    });
}

function viewPercentWin(gamers) {
    $('.percent').each(function() {
        var id = $(this).attr('id');
        var sumPriceGamer = $(this).attr('price');
        var percentWin = getPercent(sumPriceGamer, gamers);

        console.log($(this).attr('nick') + ' - Процент на победу: ' + percentWin.toFixed(2));

        counterAnimation('#' + id, Math.round(percentWin));
    });
}

function getPercent(yourPrice, gamers) {
    return yourPrice / getSumPrice(gamers) * 100;
}

function getSumPrice(gamers) {
    var sumPrice = 0;
    for (var i = 0; i < gamers.length; i++) {
        sumPrice += gamers[i]['guns']['price'];
    }
    return parseFloat(sumPrice.toFixed(2));
}

function viewSumPrice(gamers) {
    var sumPrice = getSumPrice(gamers);
    console.log('%cОбщая сумма: ' + sumPrice, 'font-weight: bold');
    counterAnimation('.sumPrice .before', sumPrice);
    counterAnimationFractionalNumber('.sumPrice .after', sumPrice);
}

function htmlGamer(gamers, i) {
    $('<div>', {
        class: 'col-xs-12 col-md-10 col-md-offset-1 gamer-wrapper',
        css: {
            'display': 'none'
        },
        append: $('<div>', {
            id: gamers[i]['nick'].toUpperCase(),
            class: 'gamer',
            append: $('<div>', {
                class: 'gamer-photo',
                css: {
                    backgroundImage: 'url(' + gamers[i]['photo'] + ')'
                }
            }).add(
                $('<div>', {
                    class: 'info',
                    append: $('<h4>', {
                        append: $('<span>', {
                            class: 'gamer-name',
                            text: gamers[i]['nick']
                        }).add(
                            $('<span>', {
                                class: 'percent-wrapper',
                                text: '%',
                                prepend: $('<span>', {
                                    nick: gamers[i]['nick'],
                                    price: gamers[i]['guns']['price'].toFixed(0),
                                    comma: gamers[i]['guns']['price'],
                                    id: gamers[i]['nick'] + gamers[i]['guns']['price'].toFixed(0),
                                    class: 'percent',
                                    text: getPercent(gamers[i]['guns']['price'], gamers).toFixed(0)
                                })
                            })
                        )
                    }).add(
                        $('<div>', {
                            class: 'guns-wrapper',
                            append: $('<ul>', {
                                class: 'gamer-guns'
                            })
                        })
                    )
                })
            )
        }).add($('<hr>'))
    }).prependTo('.list-gamers .row');

    // gamers[i]['guns'] = JSON.parse(gamers[i]['guns']);

    for (var j = 0, len = gamers[i]['guns']['guns'].length; j < len; j++) {
        $('<li>', {
            alt: gamers[i]['guns']['guns'][j]['name'],
            css: {
                backgroundImage: 'url(' + gamers[i]['guns']['guns'][j]['photo'] + ')'
            },
            append: $('<p>', {
                class: 'price',
                text: parseFloat(gamers[i]['guns']['guns'][j]['price']).toFixed(2)
            })
        }).appendTo('.list-gamers ' + '#' + gamers[i]['nick'].toUpperCase() + ' .gamer-guns');
    }

    $('<li>', {
        class: 'price',
        text: '= ' + gamers[i]['guns']['price'].toFixed(2)
    }).appendTo('.list-gamers ' + '#' + gamers[i]['nick'].toUpperCase() + ' .gamer-guns');
}

/**
 * Отображаем всех игроков в лотерее
 * @param gamers array Список всех игроков
 * @param view boolean (true - отобразить, false - скрыть)
 * @param onlyWinner boolean (true - вывод только победителя, false - вывод всех игроков в лотерее)
 * @param admin boolean
 */
function viewGamers(gamers, view, onlyWinner, admin) {
    textSumGamers.text('Количество игроков: ' + gamers.length);

    console.clear();
    if (view != false) {
        viewSumPrice(gamers);
    }
    viewPercentWin(gamers);

    if (view == true) {
        if (onlyWinner == true) {
            for (var i = 0, len = gamers.length; i < len; i++) {
                if (gamers[i]['role'] == 'winner' && !$('.list-gamers').find('#' + gamers[i]['nick'].toUpperCase()).exists()) {
                    htmlGamer(gamers, i);
                    $('.gamer-wrapper').slideDown(800);
                }
            }
        } else {
            for (var i = 0, len = gamers.length; i < len; i++) {
                if (!$('.list-gamers').find('#' + gamers[i]['nick'].toUpperCase()).exists()) {
                    htmlGamer(gamers, i);

                    if (admin) {
                        // $('<button>', {
                        //     class: 'btn indicateWinner',
                        //     id: gamers[i]['id'],
                        //     text: 'Победитель'
                        // }).prependTo('.list-gamers ' + '#' + gamers[i]['nick'].toUpperCase() + ' .gamer-photo');
                        $('<form>', {
                            action: '/admin/indicateWinner',
                            method: 'post',
                            class: 'form-indicate',
                            append: $('<input>', {
                                type: 'text',
                                class: 'hidden',
                                name: 'id',
                                value: gamers[i]['id']
                            }).add(
                                $('<input>', {
                                    type: 'submit',
                                    class: 'btn indicateWinner',
                                    value: 'Победитель'
                                })
                            )
                        }).prependTo('.list-gamers ' + '#' + gamers[i]['nick'].toUpperCase() + ' .gamer-photo');
                    }

                    $('.gamer-wrapper').slideDown(800);
                }
            }
        }
    } else {
        if ($('.list-gamers .gamer-wrapper').exists()) {
            $('.gamer-wrapper').each(function() {
                $(this).slideUp(800, function() {
                    $(this).remove();
                });
            });
        }
    }
}

/**
 * Удаляем отображение всех игроков, кроме одного указанного игрока
 * @param nick Ник игрока, какого не нужно удалять
 */
function deleteOtherGamers(nick) {
    if ($('.list-gamers .gamer-wrapper').exists()) {
        $('.gamer-wrapper').each(function() {
            if (!$(this).find('#' + nick.toUpperCase()).exists()) {
                $(this).slideUp(800, function() {
                    $(this).remove();
                });
            }
        });
    }
}

/**
 * Скрываем кнопку под аватаркой
 */
function deleteBtnIndicate() {
    $('.form-indicate').each(function() {
        $(this).fadeOut(800, function() {
            $(this).remove();
        });
    });
}

/**
 * Отображение анимации загрузки
 * @param view (true - отобразить, false - скрыть)
 */
function viewLoaderGamers(view) {
    if (view == true) {
        if (!$('.list-gamers #loader_in_lottery').exists()) {
            $('<div>', {
                id: 'loader_in_lottery',
                class: 'col-xs-12 col-md-10 col-md-offset-1',
                append:  $('<div>', {
                    id: 'circularG',
                    append:
                                ($('<div>', {id: 'circularG_1', class: 'circularG'}))
                            .add($('<div>', {id: 'circularG_2', class: 'circularG'}))
                            .add($('<div>', {id: 'circularG_3', class: 'circularG'}))
                            .add($('<div>', {id: 'circularG_4', class: 'circularG'}))
                            .add($('<div>', {id: 'circularG_5', class: 'circularG'}))
                            .add($('<div>', {id: 'circularG_6', class: 'circularG'}))
                            .add($('<div>', {id: 'circularG_7', class: 'circularG'}))
                            .add($('<div>', {id: 'circularG_8', class: 'circularG'}))
                })
            }).appendTo('.list-gamers .row');
            $('#loader_in_lottery').slideDown(800);
        }
    } else {
        if ($('.list-gamers #loader_in_lottery').exists()) {
            $('#loader_in_lottery').slideUp(800, function() {
                $(this).remove();
            });
        }
    }
}

/**
 * Освежаем время на таймере
 * @param data array Данные о таймере
 */
function refreshTime(data) {
    if (data['state'] == COUNTING_TIME) {
        initializeLottery(data['time']);
    } else if(data['state'] == COUNTING_PAUSE) {
        initializePause(data['time'], data['last_winner'], true);
    }
}

var flag = false;

/**
 * Запускаем счетчик лотереи
 */
function initializeLottery(toTime) {
    stateForTime();

    clearInterval(timeInterval);

    // ОПРЕДЕЛЯЕМ ВРЕМЯ СЧЕТЧИКА
    var compareDate = new Date();
    compareDate.setSeconds(compareDate.getSeconds() + toTime);

    // АНОНИМНАЯ ФУНКЦИЯ ДЛЯ ОБНОВЛЕНИЯ ДАННЫХ КАЖДУЮ СЕКУНДУ
    function updateClock() {
        var t = getTimeRemaining(compareDate);

        textMinutes.text(t.minutes);
        textSeconds.text(('0' + t.seconds).slice(-2));

        if (t.minutes == 0 && t.seconds <= 5 && flag == false) {
            refreshDataCycle(true);

            $.ajax({
                url: '/setup/lottery',
                type: 'post',
                success: function(data) {
                    data = JSON.parse(data);
                    for (var i = 0; i < data['gamers'].length; i++) {
                        data['gamers'][i]['guns'] = JSON.parse(data['gamers'][i]['guns']);
                    }
                    viewGamers(data['gamers'], true, false, data['admin']);
                    viewSumPrice(data['gamers']);
                    refreshTime(data);
                },
                error: function() {
                    textBlock.text(messageError);
                }
            });

            flag = true;
        }

        // ЕСЛИ ВРЕМЯ ИСТЕКЛО ВЫБИРАЕМ ПОБЕДИТЕЛЯ И ЗАПУСКАЕМ ВИЗУАЛЬНУЮ АНИМАЦИЮ
        if (t.total <= 0) {
            textMinutes.text('0');
            textSeconds.text('00');
            textTimer.animate({'color': 'red'}, 400);
            viewLoaderGamers(false);

            flag = false;

            clearInterval(timeInterval);
            clearInterval(cycleUpdateData);

            $.ajax({
                url: '/timer/getWinner',
                type: 'post',
                success: function(data) {
                    data = JSON.parse(data);
                    start(data['nick']);
                },
                error: function() {
                    textBlock.text(messageError);
                }
            });
            return false;
        }
    }

    updateClock(); // чтобы избежать задержки
    timeInterval = setInterval(updateClock, 1000);
}

function stateForPause(winner) {
    textMinutes.text('0');
    textSeconds.text('00');
    textTimer.animate({'color': 'red'}, 400);

    block.animate({
        'background-color': '#fff',
        'color': '#000'
    }, 400);
    textBlock.text(winner.toUpperCase());
}

function stateForTime() {
    textBlock.text('');
    textMinutes.text('3');
    textSeconds.text('00');
    textTimer.animate({'color': 'white'}, 800);

    block.animate({
        'background-color': '#000',
        'color': '#fff'
    }, 800);
}

/**
 * Запускаем паузу между розыгрышами
 * @param toTime date/string До какого времени будет происходить отсчет
 * @param winner string Имя победителя
 * @param viewCount boolean (true - отображать ежесекундный отсчет, false - не отображать)
 */
function initializePause(toTime, winner, viewCount) {
    stateForPause(winner);

    clearInterval(timeInterval);

    // ОПРЕДЕЛЯЕМ ВРЕМЯ ПАУЗЫ
    var timePause = new Date();
    timePause.setSeconds(timePause.getSeconds() + toTime);

    // АНОНИМНАЯ ФУНКЦИЯ ДЛЯ ОБНОВЛЕНИЯ ДАННЫХ КАЖДУЮ СЕКУНДУ
    function updateClock() {
        var t = getTimeRemaining(timePause);

        if (viewCount == true) {
            textMinutes.text(t.minutes);
            textSeconds.text(('0' + t.seconds).slice(-2));
        }

        // ЕСЛИ СЧЕТЧИК ПАУЗЫ ИСТЕК СБРАСЫВАЕМ ВСЕ ВИЗУАЛЬНЫЕ ДАННЫЕ И ЗАПУСКАЕМ СЧЕТЧИК ЛОТЕРЕИ
        if (t.total <= 0) {
            clearInterval(timeInterval);
            stateForTime();
            viewGamers([], false);
            viewLoaderGamers(true);
            dataUpdate();
        }
    }

    updateClock(); // чтобы избежать задержки
    timeInterval = setInterval(updateClock, 1000);
}

/**
 * Интерпретирует поданное на вход время
 * @param endTime string До какого времени будет происходить отсчет
 * @returns {{total: number, days: number, hours: number, minutes: number, seconds: number}}
 */
function getTimeRemaining(endTime) {
    var t = Date.parse(endTime) - Date.parse(new Date().toString());

    var seconds = Math.floor( (t/1000) % 60 );
    var minutes = Math.floor( (t/1000/60) % 60 );
    var hours = Math.floor( (t/(1000*60*60)) % 24 );
    var days = Math.floor( t/(1000*60*60*24) );
    return {
        'total': t,
        'days': days,
        'hours': hours,
        'minutes': minutes,
        'seconds': seconds
    };
}

/**
 * Запускаем анимацию отображения имени победителя. На вход получаем имя победителя в виде строки
 * @param winner string Ник победителя
 */
function start(winner) {
    textMinutes.text('0');
    textSeconds.text('00');
    textTimer.animate({'color': 'red'}, 400);

    selected = winner.toUpperCase();
    // Изменяем все символы, кроме пробела на нижнее подчеркивание
    covered = selected.replace(/[^\s]/g, '_');

    // textBlock.text(covered);
    timer = setInterval(decode, 60);
}

/**
 * Заменяет каждый символ в имени победителя на рандомный, если символ совпадает, оставляем
 * и до тех пор, пока вся строка не будет равной имени победителя
 */
function decode() {
    // replacements = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghiklmnopqrstuvwxyz%!@&*#_ ';
    replacements = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    replacementsLen = replacements.length;

    // split - разъеденяем и получаем массив в виде каждого символа
    // map - цикл, с помощью какого проходим каждый элемент массива и для каждого вызываем указанный метод
    // join - соединяем все элементы массива воедино
    newText = covered.split('').map(changeLetter()).join('');
    if (newText == selected) {
        clearInterval(timer);
        textBlock.text(newText);
        winnerRevealed(newText);
        return false;
    }
    covered = newText;
    textBlock.text(newText);
}

/**
 * Замена поданого на вход символа, на рандомный из указанного списка
 */
function changeLetter() {
    return function(letter, index, err) {
        if (selected[index] == letter) {
            return letter;
        } else {
            return replacements[Math.random() * replacementsLen | 0];
        }
    }
}

/**
 * Вызывается, когда победитель лотереи найден. Запускаем виз. анимацию и устанавливаем паузу
 */
function winnerRevealed(winner) {
    block.animate({
        'background-color': '#fff',
        'color': '#000'
    }, 400);

    deleteOtherGamers(winner);
    deleteBtnIndicate();

    // $color = $('.main-line-wrapper').find('h5').css('color');
    //
    // colorWinner = $('#' + winner);
    //
    // $('.gamer-wrapper').find('hr').animate({
    //     'border-color': $color
    // }, 800);
    // colorWinner.find('.gamer-photo').animate({
    //     'border-color': $color
    // }, 800);
    // colorWinner.find('.gamer-name').animate({
    //     'color': $color
    // }, 800);
    // colorWinner.find('.gamer-guns li').animate({
    //     'border-color': $color
    // }, 800);
    // colorWinner.find('li.price').animate({
    //     'color': $color
    // }, 800);

    dataUpdate(true);
    refreshDataCycle();
}

/**
 * Проверка на существование DOM-элемента
 * @returns {jQuery}
 */
jQuery.fn.exists = function() {
    return $(this).length;
};