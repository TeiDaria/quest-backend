<?php

declare(strict_types=1);

namespace App\Http;

use App\Http\Controllers\QuestController;

class Router
{
    private QuestController $questController;

    public function __construct()
    {
        $this->questController = new QuestController();
    }

    public function dispatch(string $method, string $uri): void
    {
        // убираем query-параметры (всё, что после ?)
        $path = parse_url($uri, PHP_URL_PATH);
        
        // убираем слэш в конце, если есть
        $path = rtrim($path, '/');

        // маршруты
        if ($method === 'GET' && $path === '/api/quests') {
            $this->questController->getAll();
        } 
        elseif ($method === 'POST' && preg_match('#^/api/quests/(\d+)/start$#', $path, $matches)) {
            $this->questController->start((int)$matches[1]);
        } 
        elseif ($method === 'GET' && $path === '/api/progress') {
            $this->questController->getState();
        } 
        elseif ($method === 'POST' && $path === '/api/progress/choose') {
            $this->questController->choose();
        } 
        else {
            http_response_code(404);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Маршрут не найден'], JSON_UNESCAPED_UNICODE);
        }
    }
}