<?php

require_once __DIR__ . '/Routing.php';
require_once __DIR__ . '/src/controllers/AppController.php'; 
require_once __DIR__ . '/src/controllers/SecurityController.php';
require_once __DIR__ . '/src/controllers/DashboardController.php';
require_once __DIR__ . '/src/controllers/HabitController.php';
require_once __DIR__ . '/src/controllers/AdminController.php';

$router = Router::getInstance();

$security = new SecurityController();
$dashboard = new DashboardController();
$habitController = new HabitController();
$adminController = new AdminController();

// --- MAPOWANIE TRAS ---
$router->add('GET', '', fn() => header('Location: /login'));
$router->add('GET', 'login', fn() => $security->showLogin());
$router->add('POST', 'login', fn() => $security->handleLogin());
$router->add('GET', 'logout', fn() => $security->logout());
$router->add('GET', 'dashboard', fn() => $dashboard->dashboard());

// Ważne: nazwa metody w kontrolerze musi się zgadzać (adminPanel)
$router->add('GET', 'admin', fn() => $adminController->adminPanel());

$router->add('GET', 'addHabit', fn() => $habitController->addHabit());
$router->add('POST', 'addHabit', fn() => $habitController->addHabit());
$router->add('GET', 'water/(?P<id>\d+)', fn($params) => $habitController->water($params));
$router->add('GET', 'waterAll', fn() => $habitController->waterAll());

// --- URUCHOMIENIE ---
$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

$router->dispatch($_SERVER['REQUEST_METHOD'], (string)$path);