## ФУНКЦИОНАЛЬНЫЕ ТЕСТЫ

Фукнциональные тесты в общем-то выполняют подобную задачу приемечных тестам. Он также открывает браузер, переходит на сайт, кликает по ссылкам, отправляет формы, но когда возникает ошибка он может сообщить Вам, что именно произошло он также может проверить базу данных на наличие ожидаемых данных.

Функциональные тесты запускаются без эмуляции браузера. Для функциональных тестов нам приходится эмулировать веб-запрос и посылать его в наше приложение. Приложение, в свою очередь, должно вернуть нам ответ. Получив ответ, мы можем проанализировать его и сделать выводы о корректности работы приложения, кроме того мы имеем доступ к "внутренностям" нашего приложения.

----------------

#### ОСОБЕННОСТИ

- _Значительно быстрее приемочных тестов_
- _Предоставляют больше информации об ошибках_
- _Невозможно тестировать javascript и ajax-запросы_
- _Также могут приводить к ложным результатам_
- _Требуется фреймворк_