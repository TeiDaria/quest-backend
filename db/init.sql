SET NAMES utf8mb4;
CREATE DATABASE IF NOT EXISTS quest_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quest_db;

-- Таблица квестов
CREATE TABLE IF NOT EXISTS quests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_node_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица сюжетных узлов
CREATE TABLE IF NOT EXISTS nodes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quest_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    is_ending BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (quest_id) REFERENCES quests(id) ON DELETE CASCADE
)DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Добавляем связь start_node_id
ALTER TABLE quests ADD CONSTRAINT fk_start_node FOREIGN KEY (start_node_id) REFERENCES nodes(id) ON DELETE SET NULL;

-- Таблица переходов (Adjacency List)
CREATE TABLE IF NOT EXISTS transitions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quest_id INT NOT NULL,
    from_node_id INT NOT NULL,
    to_node_id INT NOT NULL,
    choice_text VARCHAR(255) NOT NULL,
    FOREIGN KEY (quest_id) REFERENCES quests(id) ON DELETE CASCADE,
    FOREIGN KEY (from_node_id) REFERENCES nodes(id) ON DELETE CASCADE,
    FOREIGN KEY (to_node_id) REFERENCES nodes(id) ON DELETE CASCADE
);

-- Таблица игроков
CREATE TABLE IF NOT EXISTS players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица прогресса
CREATE TABLE IF NOT EXISTS progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    quest_id INT NOT NULL,
    current_node_id INT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_player_quest (player_id, quest_id),
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (quest_id) REFERENCES quests(id) ON DELETE CASCADE,
    FOREIGN KEY (current_node_id) REFERENCES nodes(id) ON DELETE CASCADE
)DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ТЕСТОВЫЕ ДАННЫЕ
INSERT INTO quests (title, description) VALUES ('Тайна старого особняка', 'Вам предстоит исследовать заброшенный дом.');

INSERT INTO nodes (quest_id, title, content, is_ending) VALUES 
(1, 'Вход', 'Вы стоите перед дверью особняка. Куда пойдете?', FALSE),
(1, 'Внутри', 'Вы вошли в темный холл. Слышен странный шум наверху.', FALSE),
(1, 'Смерть', 'Вы упали в подвал и разбились. Игра окончена.', TRUE),
(1, 'Победа', 'Вы нашли сундук с сокровищами! Вы победили.', TRUE);

UPDATE quests SET start_node_id = 1 WHERE id = 1;

INSERT INTO transitions (quest_id, from_node_id, to_node_id, choice_text) VALUES 
(1, 1, 2, 'Войти в дом'),
(1, 1, 3, 'Обойти дом сзади'),
(1, 2, 4, 'Подняться наверх'),
(1, 2, 3, 'Открыть странную дверь в полу');

INSERT IGNORE INTO players (id, username) VALUES (1, 'TestPlayer');