<?php
require __DIR__ . '/Routing.php';
require __DIR__ . '/src/controllers/SecurityController.php';
require __DIR__ . '/src/controllers/DashboardController.php';
require __DIR__ . '/src/controllers/HabitController.php';
require __DIR__ . '/src/controllers/AdminController.php';

$router = Router::getInstance();
$security = new SecurityController();
$dashboard = new DashboardController(); // Zmienna nazywa się $dashboard
$habitController = new HabitController();
$adminController = new AdminController();

$router->add('GET', '/login', fn() => $security->showLogin());
$router->add('POST', '/login', fn() => $security->handleLogin());
$router->add('GET', '/logout', fn() => $security->logout());

// POPRAWKA: Zmiana $dashboardController na $dashboard i dodanie średnika ;
$router->add('GET', '/dashboard', fn() => $dashboard->dashboard());

$router->add('GET', '/addHabit', fn() => $habitController->addHabit());
$router->add('POST', '/addHabit', fn() => $habitController->addHabit());
$router->add('GET', '/register', fn() => $security->showRegister());
$router->add('POST', '/register', fn() => $security->handleRegister());

// Trasy z parametrami
$router->add('GET', '/water/(?P<id>\d+)', fn($params) => $habitController->water($params));
$router->add('GET', '/deleteHabit/(?P<id>\d+)', fn($params) => $habitController->delete($params));

// Panel Admina i Banowanie
$router->add('GET', '/admin', fn() => $adminController->adminPanel());
$router->add('POST', '/ban/(?P<id>\d+)', fn($params) => $adminController->ban($params));
$router->add('POST', '/unban/(?P<id>\d+)', fn($params) => $adminController->unban($params));

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);