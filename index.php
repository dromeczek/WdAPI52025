<?php
require __DIR__ . '/Routing.php';
require __DIR__ . '/src/controllers/SecurityController.php';
require __DIR__ . '/src/controllers/DashboardController.php';

$router    = Router::getInstance();
$security  = new SecurityController();
$dashboard = new DashboardController();

// ========== TRASY ==========
$router->add('GET',  '/',          fn()      => $security->showLogin());
$router->add('GET',  '/login',     fn()      => $security->showLogin());
$router->add('POST', '/login',     fn()      => $security->handleLogin());
$router->add('POST', '/logout',    fn()      => $security->logout());
$router->add('GET',  '/dashboard', fn()      => $dashboard->index());

// przykład regex z parametrem: /users/123
$router->add('GET', '/users/(?P<id>\d+)', function(array $p) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Użytkownik ID = " . (int)$p['id'];
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
