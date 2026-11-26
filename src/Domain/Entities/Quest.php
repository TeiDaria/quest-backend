<?php

declare(strict_types=1);

namespace App\Domain\Entities;

class Quest
{
    public function __construct(
        private int $id,
        private string $title,
        private string $description,
        private ?int $startNodeId
    ) {}

    public function getId(): int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getStartNodeId(): ?int { return $this->startNodeId; }
}