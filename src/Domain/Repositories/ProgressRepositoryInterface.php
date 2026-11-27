<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\PlayerProgress;

interface ProgressRepositoryInterface
{
    public function getProgressByPlayerAndQuest(int $playerId, int $questId): ?PlayerProgress;
    
    public function saveProgress(PlayerProgress $progress): void;
    
    public function createProgress(int $playerId, int $questId, int $startNodeId): void;
}