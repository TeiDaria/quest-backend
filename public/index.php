<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

// Автозагрузчик классов (PSR-4)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

use App\Infrastructure\Database;

try {
    // Подключение к бд
    $pdo = Database::getConnection();
    
    // Тестовый запрос
    $stmt = $pdo->query("SELECT id, title FROM quests");
    $quests = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'message' => 'Database connected successfully!',
        'data' => $quests
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}