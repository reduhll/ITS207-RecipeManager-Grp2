CREATE DATABASE IF NOT EXISTS recipe_manager
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE recipe_manager;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS recipes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    category ENUM('Breakfast', 'Lunch', 'Dinner', 'Dessert') NOT NULL,
    ingredients TEXT NOT NULL,
    instructions TEXT NOT NULL,
    difficulty ENUM('Easy', 'Medium', 'Hard') NOT NULL,
    preparation_time SMALLINT UNSIGNED NOT NULL,
    status ENUM('Not Cooked', 'Cooked') NOT NULL DEFAULT 'Not Cooked',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX recipes_user (user_id)
) ENGINE=InnoDB;
