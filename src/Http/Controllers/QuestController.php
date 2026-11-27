<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\UseCases\GetCurrentStateUseCase;
use App\Application\UseCases\MakeChoiceUseCase;
use App\Application\UseCases\StartQuestUseCase;
use App\Infrastructure\Repositories\MySqlProgressRepository;
use App\Infrastructure\Repositories\MySqlQuestRepository;
use Exception;

class QuestController
{
    private MySqlQuestRepository $questRepo;
    private MySqlProgressRepository $progressRepo;

    public function __construct()
    {
        $this->questRepo = new MySqlQuestRepository();
        $this->progressRepo = new MySqlProgressRepository();
    }

    // GET /api/quests
    public function getAll(): void
    {
        $quests = $this->questRepo->getAllQuests();
        $data = array_map(fn($q) => [
            'id' => $q->getId(),
            'title' => $q->getTitle(),
            'description' => $q->getDescription()
        ], $quests);
        
        $this->jsonResponse(['data' => $data]);
    }

    // POST /api/quests/{id}/start
    public function start(int $questId): void
    {
        try {
            // для простоты пока считаем, что игрок у нас всегда id=1
            $playerId = 1; 
            
            $useCase = new StartQuestUseCase($this->questRepo, $this->progressRepo);
            $result = $useCase->execute($playerId, $questId);

            $this->jsonResponse([
                'status' => 'success',
                'quest_title' => $result['quest_title'],
                'current_node' => [
                    'id' => $result['node']->getId(),
                    'title' => $result['node']->getTitle(),
                    'content' => $result['node']->getContent(),
                    'is_ending' => $result['node']->isEnding()
                ]
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    // POST /api/progress/choose
    public function choose(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $transitionId = $input['transition_id'] ?? null;
            $questId = $input['quest_id'] ?? 1;
            $playerId = 1;

            if (!$transitionId) {
                throw new Exception("Не передан transition_id");
            }

            $useCase = new MakeChoiceUseCase($this->questRepo, $this->progressRepo);
            $result = $useCase->execute($playerId, $questId, (int)$transitionId);

            $this->jsonResponse([
                'status' => 'success',
                'message' => $result['message'],
                'current_node' => [
                    'id' => $result['node']->getId(),
                    'title' => $result['node']->getTitle(),
                    'content' => $result['node']->getContent(),
                    'is_ending' => $result['node']->isEnding()
                ]
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    // GET /api/progress
    public function getState(): void
    {
        try {
            $questId = $_GET['quest_id'] ?? 1;
            $playerId = 1;

            $useCase = new GetCurrentStateUseCase($this->questRepo, $this->progressRepo);
            $result = $useCase->execute($playerId, (int)$questId);

            $choices = array_map(fn($t) => [
                'transition_id' => $t->getId(),
                'text' => $t->getChoiceText()
            ], $result['choices']);

            $this->jsonResponse([
                'status' => 'success',
                'node' => [
                    'id' => $result['node']->getId(),
                    'title' => $result['node']->getTitle(),
                    'content' => $result['node']->getContent(),
                    'is_ending' => $result['node']->isEnding()
                ],
                'choices' => $choices
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    private function jsonResponse(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}