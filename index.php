<?php

require_once __DIR__ . '/Routing.php';

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
Routing::run($path);
