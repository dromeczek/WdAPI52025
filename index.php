<?php
require __DIR__ . '/Routing.php';
require __DIR__ . '/src/controllers/SecurityController.php';
require __DIR__ . '/src/controllers/DashboardController.php';
require __DIR__ . '/src/controllers/HabitController.php';
require __DIR__ . '/src/controllers/AdminController.php';

$router = Router::getInstance();
$security = new SecurityController();
$dashboard = new DashboardController();
$habitController = new HabitController();
$adminController = new AdminController();

$router->add('GET', '/login', fn() => $security->showLogin());
$router->add('POST', '/login', fn() => $security->handleLogin());
$router->add('GET', '/logout', fn() => $security->logout());
$router->add('GET', '/dashboard', fn() => $dashboard->index());
$router->add('GET', '/addHabit', fn() => $habitController->addHabit());
$router->add('POST', '/addHabit', fn() => $habitController->addHabit());
$router->add('GET', '/register', fn() => $security->showRegister());
// Opcjonalnie dodaj też trasę dla wysyłania formularza (POST)
$router->add('POST', '/register', fn() => $security->handleRegister());
// Trasy z parametrami
$router->add('GET', '/water/(?P<id>\d+)', fn($params) => $habitController->water($params));
$router->add('GET', '/deleteHabit/(?P<id>\d+)', fn($params) => $habitController->delete($params));
$router->add('POST', '/ban/(?P<id>\d+)', fn($params) => $adminController->ban($params));
// Panel Admina
$router->add('GET', '/admin', fn() => $adminController->adminPanel());
$router->add('POST', '/unban/(?P<id>\d+)', fn($params) => $adminController->unban($params));
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);