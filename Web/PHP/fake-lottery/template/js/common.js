/**
 * Для получения текущего состояния окна (активно оно, или нет у пользователя)
 * @type {boolean}
 */
var focusBrowserTab = true;

/**
 * ВКЛАДКА БРАУЗЕРА АКТИВНА
 */
window.onfocus = function () {
    focusBrowserTab = true;
    // $('.browserTabNoActive').fadeOut(200);
    // dataUpdate(true);
    // refreshDataCycle(); // Запускаем цикл обновление данных о таймере и самом розыгрыше
};

/**
 * ВКЛАДКА БРАУЗЕРА НЕ АКТИВНА
 */
window.onblur = function() {
    focusBrowserTab = false;
    // $('.browserTabNoActive').fadeIn(200).css('display', 'table');
    // refreshDataCycle(true); // Уничтожаем цикл обновления данных о таймере и самом розыгрыше
};

/**
 * УСТАНОВКА АКТИВНОГО МЕНЮ ЭЛЕМЕНТА
 */
$(function() {
    $('.menu-elements').find('a').each(function () {
        var location = window.location.href; // получаем адрес страницы
        var link = this.href; // получаем адрес найденной ссылки

        if(location == link) {
            $(this).css({
                'color': '#fff'
            });
        }
    });

    $('.menu-block').find('a').each(function () {
        var location = window.location.href; // получаем адрес страницы
        var link = this.href; // получаем адрес найденной ссылки

        if(location == link) {
            $(this).css({
                'color': '#fff'
            });
        }
    });
});

/**
 * ОТОБРАЖЕНИЕ НАВИГАЦИОННОГО МЕНЮ
 */
$(function() {
    $('.menu-navicon').click(function() {
        $('.menu-block').fadeToggle(400);
    });

    // При изменении ширины экрана, меню будет исчезать
    $(window).resize(function() {
        $('.menu-block').fadeOut(400);
    });
});

/**
 * ОТОБРАЖЕНИЕ ВЫХОДА ИЗ СИНХРОНИЗАЦИИ
 */
$(function() {
    $('header .info-user').find('.photo').mouseenter(function() {
        $('.logout').fadeToggle(400);
    });
});

/**
 * СОХРАНЕНИЕ STEAM ССЫЛКИ ДЛЯ ОБМЕНА
 */
$(function() {
    var messageError = $('.message-error');
    $('#saveLink').click(function() {
        var link = $('#link').val();
        if (link != '') {
            $.ajax({
                url: '/steam/link',
                type: 'post',
                data: {link: link},
                success: function(data) {
                    if (data == 1) {
                        messageError.text('Сохранено!').css('color', 'green').fadeIn(200).delay(1500).fadeOut(800);
                    } else {
                        messageError.text('Вы не верно ввели адрес.');
                    }
                },
                error: function() {
                    messageError.text('ERROR!');
                }
            });
        } else {
            messageError.text('Пожалуйста, заполните поле.');
        }
    });
});

// function counterAnimation(attribute, num) {
//     var time = 3;
//     $(attribute).each(function() {
//         var i = $(this).text() == "" ? 1 : $(this).text(),
//             step = 1000 * time / (num - i), // время изменения
//             that = $(this), // куда вставлять
//             int;
//         if (i <= num) {
//             int = setInterval(function() {
//                 if (i <= num) {
//                     that.html(i);
//                 } else {
//                     clearInterval(int);
//                 }
//                 i++;
//             }, step);
//         } else if (i > num) {
//             int = setInterval(function() {
//                 if (i >= num) {
//                     that.html(i);
//                 } else {
//                     clearInterval(int);
//                 }
//                 i--;
//             }, step);
//         }
//     });
// }

/**
 * Анимационный счетчик
 * @param attribute DOM элемент
 * @param num float До какого числа изменять. Принимает float и запускает разную анимацию на две стороны числа
 */
function counterAnimation(attribute, num) {
    var time = 3;
    $(attribute).each(function() {
        var i = $(this).text() == "" ? 1 : $(this).text(),
            // num = $(this).attr('data-sum'), // до какого числа изменять
            step = Math.abs((i < num) ? 1000 * time / (num - i) : (i > num) ? 1000 * time / (i - num) : 100), // время изменения
            that = $(this),
            int;
        if (i <= num) {
            (function inner() {
                if (i <= num) {
                    if (focusBrowserTab == false) {
                        that.text(('' + num).split('.')[0]);
                    } else {
                        that.text(i);
                        int = setTimeout(inner, step);
                    }
                } else {
                    if (i != num)
                        that.text(('' + num).split('.')[0]);
                    clearTimeout(int);
                }
                i++;
            })();
        } else if (i > num) {
            (function inner() {
                if (i >= num) {
                    if (focusBrowserTab == false) {
                        that.text(('' + num).split('.')[0]);
                    } else {
                        that.text(i);
                        int = setTimeout(inner, step);
                    }
                } else {
                    if (i != num)
                        that.text(('' + num).split('.')[0]);
                    clearTimeout(int);
                }
                i--;
            })();
        }
    });
}

function counterAnimationFractionalNumber(attribute, num) {
    var time = 3;
    var afterNum = parseFloat('0.' + ('' + num.toFixed(2)).split('.')[1]);
    $(attribute).each(function() {
        var i = $(this).text() == "" ? 0.01 : parseFloat('0.' + $(this).text()),
            // num = $(this).attr('data-sum'), // до какого числа изменять
            step = Math.abs(
                (('' + i.toFixed(2)).split('.')[1] < ('' + num.toFixed(2)).split('.')[1]) ?
                    1000 * time / (('' + num.toFixed(2)).split('.')[1] - ('' + i.toFixed(2)).split('.')[1]) :
                    (('' + i.toFixed(2)).split('.')[1] > ('' + num.toFixed(2)).split('.')[1]) ?
                        1000 * time / (('' + i.toFixed(2)).split('.')[1] - ('' + num.toFixed(2)).split('.')[1]) : 1000 * time), // время изменения
            that = $(this),
            int; // куда вставлять
        if (i <= afterNum) {
            (function inner() {
                if (i <= afterNum) {
                    if (focusBrowserTab == false) {
                        that.text(('' + afterNum.toFixed(2)).split('.')[1]);
                    } else {
                        that.text(('' + i.toFixed(2)).split('.')[1]);
                        int = setTimeout(inner, step);
                    }
                } else {
                    if (i != afterNum)
                        that.text(('' + afterNum.toFixed(2)).split('.')[1]);
                    clearTimeout(int);
                }
                i += 0.01;
            })();
        } else if (i > afterNum) {
            (function inner() {
                if (i >= afterNum) {
                    if (focusBrowserTab == false) {
                        that.text(('' + afterNum.toFixed(2)).split('.')[1]);
                    } else {
                        that.text(('' + i.toFixed(2)).split('.')[1]);
                        int = setTimeout(inner, step);
                    }
                } else {
                    if (i != afterNum)
                        that.text(('' + afterNum.toFixed(2)).split('.')[1]);
                    clearTimeout(int);
                }
                i -= 0.01;
            })();
        }
    });
}