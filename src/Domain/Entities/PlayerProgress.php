<?php

declare(strict_types=1);

namespace App\Domain\Entities;

class PlayerProgress
{
    public function __construct(
        private int $id,
        private int $playerId,
        private int $questId,
        private int $currentNodeId
    ) {}

    public function getId(): int { return $this->id; }
    public function getPlayerId(): int { return $this->playerId; }
    public function getQuestId(): int { return $this->questId; }
    public function getCurrentNodeId(): int { return $this->currentNodeId; }
}