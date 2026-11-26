<?php

declare(strict_types=1);

namespace App\Domain\Entities;

class Node
{
    public function __construct(
        private int $id,
        private int $questId,
        private string $title,
        private string $content,
        private bool $isEnding
    ) {}

    public function getId(): int { return $this->id; }
    public function getQuestId(): int { return $this->questId; }
    public function getTitle(): string { return $this->title; }
    public function getContent(): string { return $this->content; }
    public function isEnding(): bool { return $this->isEnding; }
}