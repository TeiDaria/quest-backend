<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

// автозагрузчик
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

use App\Http\Router;

// получаем метод и URI запроса
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// передаем управление роутеру
$router = new Router();
$router->dispatch($method, $uri);