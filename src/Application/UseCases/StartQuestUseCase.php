<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Repositories\ProgressRepositoryInterface;
use App\Domain\Repositories\QuestRepositoryInterface;
use Exception;

class StartQuestUseCase
{
    public function __construct(
        private QuestRepositoryInterface $questRepo,
        private ProgressRepositoryInterface $progressRepo
    ) {}

    public function execute(int $playerId, int $questId): array
    {
        $quest = $this->questRepo->getQuestById($questId);
        
        if (!$quest || $quest->getStartNodeId() === null) {
            throw new Exception("Квест не найден или не имеет стартового узла");
        }

        // создаем прогресс в базе (если его еще нет)
        $this->progressRepo->createProgress($playerId, $questId, $quest->getStartNodeId());

        // возвращаем первый узел
        $node = $this->questRepo->getNodeById($quest->getStartNodeId());
        
        return [
            'quest_title' => $quest->getTitle(),
            'node' => $node
        ];
    }
}