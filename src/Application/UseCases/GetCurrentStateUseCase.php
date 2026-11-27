<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Repositories\ProgressRepositoryInterface;
use App\Domain\Repositories\QuestRepositoryInterface;
use Exception;

class GetCurrentStateUseCase
{
    public function __construct(
        private QuestRepositoryInterface $questRepo,
        private ProgressRepositoryInterface $progressRepo
    ) {}

    public function execute(int $playerId, int $questId): array
    {
        $progress = $this->progressRepo->getProgressByPlayerAndQuest($playerId, $questId);
        
        if (!$progress) {
            throw new Exception("Вы еще не начали этот квест.");
        }

        $currentNode = $this->questRepo->getNodeById($progress->getCurrentNodeId());
        $transitions = $this->questRepo->getTransitionsFromNode($currentNode->getId());

        return [
            'node' => $currentNode,
            'choices' => $transitions
        ];
    }
}