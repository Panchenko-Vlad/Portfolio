<?php

/**
 * Класс Router
 * Компонент для работы с маршрутами
 */
class Router
{
    /**
     * Свойство для хранения массива роутов
     * @var array
     */
    private $routes;

    /**
     * Конструктор
     */
    public function __construct()
    {
        // Получаем путь к файлу со всеми роутами
        $routesPath = ROOT . '/config/routes.php';
        // Подключаем этот файл и вслед за ним получаем ассоциативный массив всех роутов
        $this->routes = include($routesPath);
    }

    /**
     * Возвращает строку запроса
     * @return string
     */
    private function getURI()
    {
        // Получаем строку запроса и проверяем её на пустоту
        if (!empty($_SERVER['REQUEST_URI'])) {
            // Удаляем из начала и конца строки слеши
            return trim($_SERVER['REQUEST_URI'], '/');
        }
    }

    /**
     * Метод для обработки запроса
     */
    public function run()
    {
        $uri = $this->getURI();

        foreach ($this->routes as $uriPattern => $path) {
            if (preg_match("~^$uriPattern$~", $uri)) {
                $internalRoute = preg_replace("~^$uriPattern$~", $path, $uri);
                $segments = explode('/', $internalRoute);
                $controllerName = ucfirst(array_shift($segments)) . 'Controller';
                $actionName = 'action' . ucfirst(array_shift($segments));
                $parameters = $segments;
                $controllerFile = ROOT . '/controllers/' . $controllerName . '.php';

                if (file_exists($controllerFile)) include_once($controllerFile);

                $controllerObj = new $controllerName;
                $result = call_user_func_array(array($controllerObj, $actionName), $parameters);

                if ($result != null) break;
            }
        }
    }
}