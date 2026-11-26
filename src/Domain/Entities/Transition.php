<?php

declare(strict_types=1);

namespace App\Domain\Entities;

class Transition
{
    public function __construct(
        private int $id,
        private int $questId,
        private int $fromNodeId,
        private int $toNodeId,
        private string $choiceText
    ) {}

    public function getId(): int { return $this->id; }
    public function getFromNodeId(): int { return $this->fromNodeId; }
    public function getToNodeId(): int { return $this->toNodeId; }
    public function getChoiceText(): string { return $this->choiceText; }
}