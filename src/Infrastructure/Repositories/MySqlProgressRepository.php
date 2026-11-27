<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\PlayerProgress;
use App\Domain\Repositories\ProgressRepositoryInterface;
use App\Infrastructure\Database;
use PDO;

class MySqlProgressRepository implements ProgressRepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getProgressByPlayerAndQuest(int $playerId, int $questId): ?PlayerProgress
    {
        $stmt = $this->pdo->prepare("SELECT * FROM progress WHERE player_id = :player_id AND quest_id = :quest_id");
        $stmt->execute(['player_id' => $playerId, 'quest_id' => $questId]);
        $row = $stmt->fetch();

        if (!$row) return null;

        return new PlayerProgress((int)$row['id'], (int)$row['player_id'], (int)$row['quest_id'], (int)$row['current_node_id']);
    }

    public function saveProgress(PlayerProgress $progress): void
    {
        $stmt = $this->pdo->prepare("UPDATE progress SET current_node_id = :node_id WHERE id = :id");
        $stmt->execute([
            'node_id' => $progress->getCurrentNodeId(),
            'id' => $progress->getId()
        ]);
    }

    public function createProgress(int $playerId, int $questId, int $startNodeId): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO progress (player_id, quest_id, current_node_id) 
            VALUES (:player_id, :quest_id, :node_id)
            ON DUPLICATE KEY UPDATE current_node_id = VALUES(current_node_id), updated_at = CURRENT_TIMESTAMP
        ");
        $stmt->execute([
            'player_id' => $playerId,
            'quest_id' => $questId,
            'node_id' => $startNodeId
        ]);
    }
}