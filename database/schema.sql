-- DATABASE
CREATE DATABASE aji_l3bo_cafe;
USE aji_l3bo_cafe;

-- USERS (ADMIN / PLAYER)
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    name_user VARCHAR(40),
    email VARCHAR(100) UNIQUE,
    pass_word VARCHAR(100),
    phone_number VARCHAR(15),
    role_user ENUM('admin', 'player') DEFAULT 'player',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABLES (PHYSICAL TABLES IN CAFE)
CREATE TABLE tables (
    id_table INT AUTO_INCREMENT PRIMARY KEY,
    name_table VARCHAR(20),
    capacity INT,
    status_table ENUM('free', 'occupied') DEFAULT 'free'
);

-- GAMES
CREATE TABLE games (
    id_game INT AUTO_INCREMENT PRIMARY KEY,
    name_game VARCHAR(50),
    players_min INT,
    players_max INT,
    duration INT, -- in minutes
    difficulty ENUM('easy','medium','hard'),
    description_game TEXT,
    status_game ENUM('available','in_use') DEFAULT 'available',
    category_game ENUM(
        'social_deduction',
        'party',
        'cooperative',
        'team',
        'trivia',
        'other'
    )
);

-- RESERVATIONS
CREATE TABLE reservations (
    id_reservation INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    id_table INT,
    people_count INT,
    reservation_date DATE,
    reservation_time TIME,
    status_reservation ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE SET NULL,
    FOREIGN KEY (id_table) REFERENCES tables(id_table) ON DELETE CASCADE
);

-- SESSIONS
CREATE TABLE sessions (
    id_session INT AUTO_INCREMENT PRIMARY KEY,
    id_reservation INT,
    id_game INT,
    id_table INT,
    start_time DATETIME,
    end_time DATETIME,
    status_session ENUM('active','finished') DEFAULT 'active',
    FOREIGN KEY (id_reservation) REFERENCES reservations(id_reservation) ON DELETE CASCADE,
    FOREIGN KEY (id_game) REFERENCES games(id_game) ON DELETE CASCADE,
    FOREIGN KEY (id_table) REFERENCES tables(id_table) ON DELETE CASCADE
);