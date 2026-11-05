CREATE DATABASE IF NOT EXISTS mordor;
USE mordor;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'soldier'
);

CREATE TABLE orc_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin user
INSERT INTO users (username, password, role) VALUES
('sauron', 'oneringtorulethemall', 'admin');

-- Pre-populate with some orc messages for atmosphere
INSERT INTO orc_messages (author, message) VALUES
('Gothmog', 'The age of Men is over. The time of the Orc has come.'),
('Ugluk', 'Looks like meat is back on the menu, boys!'),
('Lurtz', 'Find the halflings! Find the halflings!'),
('Shagrat', 'I warned you about that new watchtower guard...'),
('Gorbag', 'Has anyone seen my armor? I left it near the Black Gate.');
