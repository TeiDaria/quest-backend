<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Node;
use App\Domain\Entities\Quest;
use App\Domain\Entities\Transition;
use App\Domain\Repositories\QuestRepositoryInterface;
use App\Infrastructure\Database;
use PDO;

class MySqlQuestRepository implements QuestRepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getAllQuests(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM quests");
        $rows = $stmt->fetchAll();
        
        $quests = [];
        foreach ($rows as $row) {
            $quests[] = new Quest((int)$row['id'], $row['title'], $row['description'], $row['start_node_id'] ? (int)$row['start_node_id'] : null);
        }
        return $quests;
    }

    public function getQuestById(int $id): ?Quest
    {
        $stmt = $this->pdo->prepare("SELECT * FROM quests WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) return null;

        return new Quest((int)$row['id'], $row['title'], $row['description'], $row['start_node_id'] ? (int)$row['start_node_id'] : null);
    }

    public function getNodeById(int $id): ?Node
    {
        $stmt = $this->pdo->prepare("SELECT * FROM nodes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) return null;

        return new Node((int)$row['id'], (int)$row['quest_id'], $row['title'], $row['content'], (bool)$row['is_ending']);
    }

    public function getTransitionsFromNode(int $nodeId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM transitions WHERE from_node_id = :node_id");
        $stmt->execute(['node_id' => $nodeId]);
        $rows = $stmt->fetchAll();

        $transitions = [];
        foreach ($rows as $row) {
            $transitions[] = new Transition((int)$row['id'], (int)$row['quest_id'], (int)$row['from_node_id'], (int)$row['to_node_id'], $row['choice_text']);
        }
        return $transitions;
    }

    public function getTransitionById(int $id): ?Transition
    {
        $stmt = $this->pdo->prepare("SELECT * FROM transitions WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) return null;

        return new Transition((int)$row['id'], (int)$row['quest_id'], (int)$row['from_node_id'], (int)$row['to_node_id'], $row['choice_text']);
    }
}