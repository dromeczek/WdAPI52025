<?php
require_once __DIR__ . '/src/controllers/AppController.php';
require_once __DIR__ . '/src/controllers/SecurityController.php';
require_once __DIR__ . '/src/controllers/DashboardController.php';
require_once __DIR__ . '/src/controllers/HabitController.php';
require_once __DIR__ . '/src/controllers/AdminController.php';


class Routing
{
    private static array $routes = [
        ['GET', '#^$#', 'SecurityController', 'root'],

        // AUTH
        ['GET',  '#^login$#',                 'SecurityController',  'loginForm'],
        ['POST', '#^login$#',                 'SecurityController',  'login'],
        ['GET',  '#^register$#',              'SecurityController',  'registerForm'],
        ['POST', '#^register$#',              'SecurityController',  'register'],
        ['GET',  '#^logout$#',                'SecurityController',  'logout'],

        // DASHBOARD
        ['GET',  '#^dashboard$#',             'DashboardController', 'index'],

        // HABITS
        ['GET',  '#^addHabit$#',              'HabitController',     'form'],
        ['POST', '#^addHabit$#',              'HabitController',     'add'],
        ['GET',  '#^water/(?P<id>\d+)$#',     'HabitController',     'water'],
        ['GET',  '#^waterAll$#',              'HabitController',     'waterAll'],
        ['POST', '#^habit/delete/(?P<id>\d+)$#', 'HabitController', 'delete'],


        // ADMIN
        ['GET',  '#^admin$#',                 'AdminController',     'adminPanel'],
        ['POST', '#^ban/(?P<id>\d+)$#',       'AdminController',     'ban'],
        ['POST', '#^unban/(?P<id>\d+)$#',     'AdminController',     'unban'],
    ];

    public static function run(string $path): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        foreach (self::$routes as [$routeMethod, $pattern, $controller, $action]) {
            if ($routeMethod !== $method) {
                continue;
            }

            if (preg_match($pattern, $path, $matches)) {
                $params = array_filter(
                    $matches,
                    fn($k) => !is_int($k),
                    ARRAY_FILTER_USE_KEY
                );

                $controllerObj = new $controller();
                $controllerObj->$action($params);
                return;
            }
        }

        (new AppController())->render('404');
    }
}
