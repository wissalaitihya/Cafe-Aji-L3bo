CREATE DATABASE IF NOT EXISTS aji_l3bo_cafe;
USE aji_l3bo_cafe;

-- USERS
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    name_user VARCHAR(40) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    pass_word VARCHAR(255) NOT NULL,
    phone_number VARCHAR(15),
    role_user ENUM('admin', 'player') DEFAULT 'player',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABLES
CREATE TABLE tables (
    id_table INT AUTO_INCREMENT PRIMARY KEY,
    name_table VARCHAR(20) NOT NULL,
    capacity INT NOT NULL,
    status_table ENUM('free', 'occupied') DEFAULT 'free'
);

-- GAMES
CREATE TABLE games (
    id_game INT AUTO_INCREMENT PRIMARY KEY,
    name_game VARCHAR(50) NOT NULL,
    players_min INT NOT NULL,
    players_max INT NOT NULL,
    duration INT NOT NULL,
    difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
    description_game TEXT,
    how_to_play TEXT,
    image_game VARCHAR(255) DEFAULT NULL,
    status_game ENUM('available', 'in_use') DEFAULT 'available',
    category_game ENUM('social_deduction', 'party', 'cooperative', 'team', 'trivia', 'other') DEFAULT 'other'
);

-- RESERVATIONS
CREATE TABLE reservations (
    id_reservation INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_table INT NOT NULL,
    id_game INT DEFAULT NULL,
    people_count INT NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    reservation_end_time TIME NOT NULL,
    status_reservation ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_table) REFERENCES tables (id_table) ON DELETE CASCADE,
    FOREIGN KEY (id_game) REFERENCES games (id_game) ON DELETE SET NULL
);

-- SESSIONS
CREATE TABLE sessions (
    id_session INT AUTO_INCREMENT PRIMARY KEY,
    id_reservation INT,
    id_game INT NOT NULL,
    id_table INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME DEFAULT NULL,
    status_session ENUM('active', 'finished') DEFAULT 'active',
    FOREIGN KEY (id_reservation) REFERENCES reservations (id_reservation) ON DELETE CASCADE,
    FOREIGN KEY (id_game) REFERENCES games (id_game) ON DELETE CASCADE,
    FOREIGN KEY (id_table) REFERENCES tables (id_table) ON DELETE CASCADE
);

-- =====================
-- SEED DATA
-- =====================

-- Admin (password: admin123)
INSERT INTO users (name_user, email, pass_word, phone_number, role_user) VALUES
('Admin', 'admin@ajil3bo.ma', '$2y$10$7Q5Z5Q5Z5Q5Z5Q5Z5Q5Z5ePxKjKjKjKjKjKjKjKjKjKjKjKjKjKjK', '0600000000', 'admin');

-- Players (password: player123)
INSERT INTO users (name_user, email, pass_word, phone_number, role_user) VALUES
('Youssef', 'youssef@mail.com', '$2y$10$7Q5Z5Q5Z5Q5Z5Q5Z5Q5Z5ePxKjKjKjKjKjKjKjKjKjKjKjKjKjKjK', '0611111111', 'player'),
('Sara', 'sara@mail.com', '$2y$10$7Q5Z5Q5Z5Q5Z5Q5Z5Q5Z5ePxKjKjKjKjKjKjKjKjKjKjKjKjKjKjK', '0622222222', 'player');

-- Tables
INSERT INTO tables (name_table, capacity, status_table) VALUES
('Table 1', 4, 'free'),
('Table 2', 6, 'free'),
('Table 3', 8, 'occupied'),
('Table 4', 2, 'free');

-- Games
INSERT INTO games (name_game, players_min, players_max, duration, difficulty, description_game, how_to_play, status_game, category_game) VALUES
('Mafia', 6, 12, 30, 'medium', 'A social deduction game where players try to identify the mafia members.', 'Players are secretly assigned roles: Mafia or Civilian. Each night, the Mafia eliminates a player. Each day, everyone votes to eliminate a suspected Mafia member. Civilians win by eliminating all Mafia. Mafia wins when they equal or outnumber the Civilians.', 'available', 'social_deduction'),
('Codenames', 2, 8, 15, 'easy', 'Spymasters give one-word clues to help their team guess words on the grid.', 'Split into two teams. Each team has a Spymaster who sees which words belong to their team. Spymasters give one-word + one-number clues (e.g. "ocean 3") to guide teammates. Guess the right words and avoid the assassin word. First team to find all their agents wins.', 'available', 'team'),
('Catan', 3, 4, 90, 'medium', 'Build settlements, trade resources, and become the lord of Catan.', 'Roll dice to collect resources (wood, brick, sheep, wheat, ore). Use resources to build roads, settlements, and cities. Trade with other players or the bank. Earn Victory Points from buildings, cards, and achievements. First to 10 Victory Points wins.', 'available', 'cooperative'),
('Uno', 2, 10, 20, 'easy', 'Classic card game. Match colors and numbers to get rid of all your cards.', 'On your turn, play a card matching the top card by color or number. Use action cards (Skip, Reverse, Draw 2) to disrupt opponents. When you have one card left, shout "UNO!" or draw 2 as penalty. Wild cards let you change the color. First player to empty their hand wins.', 'available', 'party'),
('Ticket to Ride', 2, 5, 60, 'easy', 'Collect train cards and claim railway routes across the map.', 'Draw train cards and use matching sets to claim routes between cities. Complete Destination Tickets (secret city-pair goals) for bonus points. Longer routes score more points. Incomplete tickets subtract points. The player with the longest continuous route earns a bonus. Most points at the end wins.', 'available', 'cooperative'),
('Werewolf', 6, 18, 30, 'medium', 'Villagers must find and eliminate the werewolves before being eaten.', 'A moderator runs the game. Each night, Werewolves secretly choose a Villager to eliminate. Special roles (Seer, Doctor, etc.) use their powers. Each day, all players debate and vote to eliminate a suspect. Villagers win if all Werewolves are eliminated. Werewolves win if they equal or outnumber Villagers.', 'in_use', 'social_deduction'),
('Dixit', 3, 6, 30, 'easy', 'A creative storytelling game with beautiful illustrated cards.', 'The storyteller gives a clue (word, phrase, sound) for one of their cards, then places it face-down. Other players pick a card from their hand that fits the clue. All chosen cards are shuffled and revealed. Players vote for which card they think is the storyteller''s. Score points for tricking others or guessing correctly.', 'available', 'party'),
('Risk', 2, 6, 120, 'hard', 'Conquer the world through strategic troop deployment and battles.', 'Place armies on territories you control. On your turn, reinforce your territories, attack neighbors by rolling dice, and fortify positions. Conquer continents for bonus armies each turn. Eliminate opponents to capture their cards for troop bonuses. Last player standing — or first to complete a secret mission — wins.', 'available', 'team'),
('Pandemic', 2, 4, 60, 'hard', 'Work together to stop four deadly diseases from spreading across the globe.', 'Players are specialists (Medic, Scientist, etc.) working together. Each turn: take 4 actions (move, treat disease, share knowledge, build a research station), draw 2 player cards, then infect cities. Cure all 4 diseases before the outbreaks or cards run out. Communication and planning are key — no player acts alone.', 'available', 'cooperative'),
('Trivial Pursuit', 2, 6, 60, 'medium', 'Answer trivia questions from six different categories to win.', 'Roll the die and move around the board. Land on a colored space and answer a trivia question in that category (Geography, Entertainment, History, Art & Literature, Science & Nature, Sports & Leisure). Answer correctly on a wedge space to collect a pie piece. First player to fill all 6 wedges and answer a final question wins.', 'available', 'trivia'),
('Monopoly', 2, 8, 120, 'medium', 'Buy properties, build houses, and bankrupt your opponents.', 'Roll dice to move around the board. Buy properties you land on, then charge rent when others land on them. Build houses and hotels to increase rent. Use Chance and Community Chest cards for surprises. Trade properties to complete color sets. Avoid going bankrupt by carefully managing your money. Last player with money wins.', 'available', 'party'),
('Scrabble', 2, 4, 60, 'medium', 'Create words on the board using letter tiles for points.', 'Draw 7 letter tiles and place words on the board, connecting to existing words like a crossword. Score points based on letter values and bonus squares (Double/Triple Letter or Word). Challenge invalid words — if the word is invalid, the player loses their turn. Replenish tiles after each turn. Game ends when tiles run out and a player empties their rack.', 'available', 'trivia'),
('Chess', 2, 2, 45, 'hard', 'The classic strategy game of kings and queens.', 'Each piece moves differently: Pawns forward, Rooks in lines, Bishops diagonally, Knights in an L-shape, Queens anywhere, Kings one square. Capture your opponent''s pieces by moving onto their square. Put the opponent''s King in "check" (under attack). Win by achieving "checkmate" — the King is in check with no legal escape move.', 'available', 'team');

-- =====================
-- V2 MIGRATION (run on existing DB)
-- =====================
ALTER TABLE games ADD COLUMN IF NOT EXISTS how_to_play TEXT AFTER description_game;
ALTER TABLE games ADD COLUMN IF NOT EXISTS image_game VARCHAR(255) DEFAULT NULL AFTER how_to_play;

-- V3 migration: add end_time to reservations (defaults to 2h after start for existing rows)
ALTER TABLE reservations ADD COLUMN IF NOT EXISTS reservation_end_time TIME NOT NULL DEFAULT '00:00:00' AFTER reservation_time;
UPDATE reservations SET reservation_end_time = ADDTIME(reservation_time, '02:00:00') WHERE reservation_end_time = '00:00:00';

-- V4 migration: populate how_to_play for existing seed games
UPDATE games SET how_to_play = 'Players are secretly assigned roles: Mafia or Civilian. Each night, the Mafia eliminates a player. Each day, everyone votes to eliminate a suspected Mafia member. Civilians win by eliminating all Mafia. Mafia wins when they equal or outnumber the Civilians.' WHERE name_game = 'Mafia' AND (how_to_play IS NULL OR how_to_play = '');
UPDATE games SET how_to_play = 'Split into two teams. Each team has a Spymaster who sees which words belong to their team. Spymasters give one-word + one-number clues (e.g. "ocean 3") to guide teammates. Guess the right words and avoid the assassin word. First team to find all their agents wins.' WHERE name_game = 'Codenames' AND (how_to_play IS NULL OR how_to_play = '');
UPDATE games SET how_to_play = 'Roll dice to collect resources (wood, brick, sheep, wheat, ore). Use resources to build roads, settlements, and cities. Trade with other players or the bank. Earn Victory Points from buildings, cards, and achievements. First to 10 Victory Points wins.' WHERE name_game = 'Catan' AND (how_to_play IS NULL OR how_to_play = '');
UPDATE games SET how_to_play = 'On your turn, play a card matching the top card by color or number. Use action cards (Skip, Reverse, Draw 2) to disrupt opponents. When you have one card left, shout "UNO!" or draw 2 as penalty. Wild cards let you change the color. First player to empty their hand wins.' WHERE name_game = 'Uno' AND (how_to_play IS NULL OR how_to_play = '');
UPDATE games SET how_to_play = 'Draw train cards and use matching sets to claim routes between cities. Complete Destination Tickets (secret city-pair goals) for bonus points. Longer routes score more points. Incomplete tickets subtract points. The player with the longest continuous route earns a bonus. Most points at the end wins.' WHERE name_game = 'Ticket to Ride' AND (how_to_play IS NULL OR how_to_play = '');
UPDATE games SET how_to_play = 'A moderator runs the game. Each night, Werewolves secretly choose a Villager to eliminate. Special roles (Seer, Doctor, etc.) use their powers. Each day, all players debate and vote to eliminate a suspect. Villagers win if all Werewolves are eliminated. Werewolves win if they equal or outnumber Villagers.' WHERE name_game = 'Werewolf' AND (how_to_play IS NULL OR how_to_play = '');
UPDATE games SET how_to_play = 'The storyteller gives a clue (word, phrase, sound) for one of their cards, then places it face-down. Other players pick a card from their hand that fits the clue. All chosen cards are shuffled and revealed. Players vote for which card they think is the storyteller''s. Score points for tricking others or guessing correctly.' WHERE name_game = 'Dixit' AND (how_to_play IS NULL OR how_to_play = '');
UPDATE games SET how_to_play = 'Place armies on territories you control. On your turn, reinforce your territories, attack neighbors by rolling dice, and fortify positions. Conquer continents for bonus armies each turn. Eliminate opponents to capture their cards for troop bonuses. Last player standing — or first to complete a secret mission — wins.' WHERE name_game = 'Risk' AND (how_to_play IS NULL OR how_to_play = '');
UPDATE games SET how_to_play = 'Players are specialists (Medic, Scientist, etc.) working together. Each turn: take 4 actions (move, treat disease, share knowledge, build a research station), draw 2 player cards, then infect cities. Cure all 4 diseases before the outbreaks or cards run out. Communication and planning are key — no player acts alone.' WHERE name_game = 'Pandemic' AND (how_to_play IS NULL OR how_to_play = '');
UPDATE games SET how_to_play = 'Roll the die and move around the board. Land on a colored space and answer a trivia question in that category (Geography, Entertainment, History, Art & Literature, Science & Nature, Sports & Leisure). Answer correctly on a wedge space to collect a pie piece. First player to fill all 6 wedges and answer a final question wins.' WHERE name_game = 'Trivial Pursuit' AND (how_to_play IS NULL OR how_to_play = '');
UPDATE games SET how_to_play = 'Roll dice to move around the board. Buy properties you land on, then charge rent when others land on them. Build houses and hotels to increase rent. Use Chance and Community Chest cards for surprises. Trade properties to complete color sets. Avoid going bankrupt by carefully managing your money. Last player with money wins.' WHERE name_game = 'Monopoly' AND (how_to_play IS NULL OR how_to_play = '');
UPDATE games SET how_to_play = 'Draw 7 letter tiles and place words on the board, connecting to existing words like a crossword. Score points based on letter values and bonus squares (Double/Triple Letter or Word). Challenge invalid words — if the word is invalid, the player loses their turn. Replenish tiles after each turn. Game ends when tiles run out and a player empties their rack.' WHERE name_game = 'Scrabble' AND (how_to_play IS NULL OR how_to_play = '');
UPDATE games SET how_to_play = 'Each piece moves differently: Pawns forward, Rooks in lines, Bishops diagonally, Knights in an L-shape, Queens anywhere, Kings one square. Capture your opponent''s pieces by moving onto their square. Put the opponent''s King in "check" (under attack). Win by achieving "checkmate" — the King is in check with no legal escape move.' WHERE name_game = 'Chess' AND (how_to_play IS NULL OR how_to_play = '');

-- =====================
-- V5 MIGRATION: Game Ratings
-- =====================
CREATE TABLE IF NOT EXISTS game_ratings (
    id_rating      INT AUTO_INCREMENT PRIMARY KEY,
    id_game        INT NOT NULL,
    id_user        INT NOT NULL,
    id_session     INT DEFAULT NULL,
    stars          DECIMAL(2,1) NOT NULL CHECK (stars BETWEEN 0.5 AND 5.0),
    comment_rating TEXT DEFAULT NULL,
    rated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_game (id_user, id_game),
    FOREIGN KEY (id_game)    REFERENCES games    (id_game)    ON DELETE CASCADE,
    FOREIGN KEY (id_user)    REFERENCES users    (id_user)    ON DELETE CASCADE,
    FOREIGN KEY (id_session) REFERENCES sessions (id_session) ON DELETE SET NULL
);