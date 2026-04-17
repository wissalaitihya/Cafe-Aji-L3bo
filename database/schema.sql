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
INSERT INTO games (name_game, players_min, players_max, duration, difficulty, description_game, status_game, category_game) VALUES
('Mafia', 6, 12, 30, 'medium', 'A social deduction game where players try to identify the mafia members.', 'available', 'social_deduction'),
('Codenames', 2, 8, 15, 'easy', 'Spymasters give one-word clues to help their team guess words on the grid.', 'available', 'team'),
('Catan', 3, 4, 90, 'medium', 'Build settlements, trade resources, and become the lord of Catan.', 'available', 'cooperative'),
('Uno', 2, 10, 20, 'easy', 'Classic card game. Match colors and numbers to get rid of all your cards.', 'available', 'party'),
('Ticket to Ride', 2, 5, 60, 'easy', 'Collect train cards and claim railway routes across the map.', 'available', 'cooperative'),
('Werewolf', 6, 18, 30, 'medium', 'Villagers must find and eliminate the werewolves before being eaten.', 'in_use', 'social_deduction'),
('Dixit', 3, 6, 30, 'easy', 'A creative storytelling game with beautiful illustrated cards.', 'available', 'party'),
('Risk', 2, 6, 120, 'hard', 'Conquer the world through strategic troop deployment and battles.', 'available', 'team'),
('Pandemic', 2, 4, 60, 'hard', 'Work together to stop four deadly diseases from spreading across the globe.', 'available', 'cooperative'),
('Trivial Pursuit', 2, 6, 60, 'medium', 'Answer trivia questions from six different categories to win.', 'available', 'trivia'),
('Monopoly', 2, 8, 120, 'medium', 'Buy properties, build houses, and bankrupt your opponents.', 'available', 'party'),
('Scrabble', 2, 4, 60, 'medium', 'Create words on the board using letter tiles for points.', 'available', 'trivia'),
('Chess', 2, 2, 45, 'hard', 'The classic strategy game of kings and queens.', 'available', 'team')