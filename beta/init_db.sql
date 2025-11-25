-- init_db.sql
CREATE DATABASE IF NOT EXISTS recipes_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE recipes_db;

CREATE TABLE IF NOT EXISTS recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255),
    description TEXT,
    ingredients TEXT,
    tools TEXT,
    tool_description TEXT,
    steps LONGTEXT,
    images TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- A small index for searching by name
CREATE INDEX idx_name ON recipes(name);
