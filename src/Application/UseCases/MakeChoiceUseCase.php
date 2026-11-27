<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Entities\PlayerProgress;
use App\Domain\Repositories\ProgressRepositoryInterface;
use App\Domain\Repositories\QuestRepositoryInterface;
use Exception;

class MakeChoiceUseCase
{
    public function __construct(
        private QuestRepositoryInterface $questRepo,
        private ProgressRepositoryInterface $progressRepo
    ) {}

    public function execute(int $playerId, int $questId, int $transitionId): array
    {
        $progress = $this->progressRepo->getProgressByPlayerAndQuest($playerId, $questId);
        
        if (!$progress) {
            throw new Exception("Прогресс не найден. Сначала начните квест.");
        }

        $transition = $this->questRepo->getTransitionById($transitionId);
        
        if (!$transition) {
            throw new Exception("Такого перехода не существует.");
        }

        // проверка: переход должен идти из того узла, где сейчас находится игрок
        if ($transition->getFromNodeId() !== $progress->getCurrentNodeId()) {
            throw new Exception("Нельзя совершить этот переход из текущего узла.");
        }

        // обновляем узел в прогрессе
        $newProgress = new PlayerProgress(
            $progress->getId(),
            $playerId,
            $questId,
            $transition->getToNodeId()
        );
        
        $this->progressRepo->saveProgress($newProgress);

        $nextNode = $this->questRepo->getNodeById($transition->getToNodeId());

        return [
            'message' => 'Выбор сделан',
            'node' => $nextNode
        ];
    }
}