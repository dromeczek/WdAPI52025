<?php

require_once __DIR__ . '/Routing.php';
// Najpierw ładujemy kontroler bazowy
require_once __DIR__ . '/src/controllers/AppController.php'; 

// Następnie ładujemy wszystkie kontrolery pochodne (zwróć uwagę na średniki na końcu!)
require_once __DIR__ . '/src/controllers/SecurityController.php';
require_once __DIR__ . '/src/controllers/DashboardController.php';
require_once __DIR__ . '/src/controllers/HabitController.php';
require_once __DIR__ . '/src/controllers/AdminController.php';

$router = Router::getInstance();

// Inicjalizacja obiektów klas
$security = new SecurityController();
$dashboard = new DashboardController();
$habitController = new HabitController();
$adminController = new AdminController();

// --- DEFINICJA TRAS (ROUTING) ---

$router->add('GET', '/login', fn() => $security->showLogin());
$router->add('POST', '/login', fn() => $security->handleLogin());
$router->add('GET', '/logout', fn() => $security->logout());
$router->add('GET', '/register', fn() => $security->showRegister());
$router->add('POST', '/register', fn() => $security->handleRegister());

// Główny widok ogrodu
$router->add('GET', '/dashboard', fn() => $dashboard->dashboard());

// Zarządzanie nawykami
$router->add('GET', '/addHabit', fn() => $habitController->addHabit());
$router->add('POST', '/addHabit', fn() => $habitController->addHabit());
$router->add('GET', '/water/(?P<id>\d+)', fn($params) => $habitController->water($params));
$router->add('GET', '/deleteHabit/(?P<id>\d+)', fn($params) => $habitController->delete($params));

// Panel Admina
$router->add('GET', '/admin', fn() => $adminController->adminPanel());
$router->add('POST', '/ban/(?P<id>\d+)', fn($params) => $adminController->ban($params));
$router->add('POST', '/unban/(?P<id>\d+)', fn($params) => $adminController->unban($params));
$router->add('GET', '/waterAll', fn() => $habitController->waterAll());
// Uruchomienie mechanizmu routingu
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);