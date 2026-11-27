<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

use App\Infrastructure\Repositories\MySqlQuestRepository;
use App\Infrastructure\Repositories\MySqlProgressRepository;
use App\Application\UseCases\StartQuestUseCase;

try {
    // инициализируем репозитории (базу)
    $questRepo = new MySqlQuestRepository();
    $progressRepo = new MySqlProgressRepository();

    // вызываем Use Case (логику старта квеста)
    $startQuest = new StartQuestUseCase($questRepo, $progressRepo);
    
    
    $quest = $questRepo->getQuestById(1);
    $node = $questRepo->getNodeById($quest->getStartNodeId());
    $choices = $questRepo->getTransitionsFromNode($node->getId());

    echo json_encode([
        'status' => 'success',
        'message' => 'Clean Architecture works!',
        'quest' => $quest->getTitle(),
        'node' => $node->getContent(),
        'choices' => array_map(fn($c) => $c->getChoiceText(), $choices)
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}