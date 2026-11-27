<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\Node;
use App\Domain\Entities\Quest;
use App\Domain\Entities\Transition;

interface QuestRepositoryInterface
{
    /** @return Quest[] */
    public function getAllQuests(): array;
    
    public function getQuestById(int $id): ?Quest;
    
    public function getNodeById(int $id): ?Node;
    
    /** @return Transition[] */
    public function getTransitionsFromNode(int $nodeId): array;
    
    public function getTransitionById(int $id): ?Transition;
}