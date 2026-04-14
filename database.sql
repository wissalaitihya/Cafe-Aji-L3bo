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
    category_game ENUM('board','card','video','other')
);

-- RESERVATIONS
CREATE TABLE reservations (
    id_reservation INT AUTO_INCREMENT PRIMARY KEY,
    people_count INT,
    reservation_date DATE,
    reservation_time TIME,
    status_reservation ENUM('pending','confirmed','cancelled') DEFAULT 'pending',

);

-- SESSIONS
CREATE TABLE sessions (
    id_session INT AUTO_INCREMENT PRIMARY KEY,
    start_time DATETIME,
    end_time DATETIME,
    status_session ENUM('active','finished') DEFAULT 'active',
);