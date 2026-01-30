<?php

require __DIR__ . '/Routing.php';
require __DIR__ . '/src/controllers/SecurityController.php';
require __DIR__ . '/src/controllers/DashboardController.php';
require __DIR__ . '/src/controllers/HabitController.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$router    = Router::getInstance();
$security  = new SecurityController();
$dashboard = new DashboardController();
$habitController = new HabitController();

// REJESTRACJA
$router->add('GET',  '/register', fn() => $security->showRegister());
$router->add('POST', '/register', fn() => $security->handleRegister());

// LOGOWANIE / WYLOGOWANIE
$router->add('GET',  '/',      fn() => $security->showLogin());
$router->add('GET',  '/login', fn() => $security->showLogin());
$router->add('POST', '/login', fn() => $security->handleLogin());

// UWAGA: logout powinien być GET (bo klik w link/przycisk w UI)
$router->add('GET', '/logout', fn() => $security->logout());

// DASHBOARD (chroniony sesją w kontrolerze)
$router->add('GET', '/dashboard', fn() => $dashboard->index());

// przykład regex z parametrem (zostawiam)
$router->add('GET', '/users/(?P<id>\d+)', function(array $p) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Użytkownik ID = " . (int)$p['id'];
});
//Habitt
$router->add('GET',  '/addHabit', fn() => $habitController->addHabit());
$router->add('POST', '/addHabit', fn() => $habitController->addHabit());

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
