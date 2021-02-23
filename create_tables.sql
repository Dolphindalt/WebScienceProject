USE three_leaf;

DROP TABLE IF EXISTS post_replies;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS threads;
DROP TABLE IF EXISTS boards;
DROP TABLE IF EXISTS files;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS passwords;

CREATE TABLE passwords (
    id INT NOT NULL AUTO_INCREMENT,
    password VARCHAR(255) NOT NULL,
    PRIMARY KEY(id)
);

INSERT INTO passwords (password) VALUES ("bruh");

CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT,
    password_id INT NOT NULL,
    username VARCHAR(36) NOT NULL UNIQUE,
    role INT NOT NULL DEFAULT 0, # 0 - normal user, 1 - moderator
    FOREIGN KEY(password_id) REFERENCES passwords(id),
    PRIMARY KEY(id)
);

INSERT INTO users (password_id, username) VALUES (1, "Daltondalt");

CREATE TABLE files (
    id INT NOT NULL AUTO_INCREMENT,
    uploader_id INT NOT NULL,
    file_name VARCHAR(40) NOT NULL UNIQUE,
    FOREIGN KEY(uploader_id) REFERENCES users(id),
    PRIMARY KEY(id)
);

INSERT INTO files (uploader_id, file_name) VALUES (1, "fdc9a303-b8e0-4c01-9455-459c7334dec7.jpg");
INSERT INTO files (uploader_id, file_name) VALUES (1, "844ca79d-5aab-438a-8abe-50edc6c8a947.jpg");

CREATE TABLE boards (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(36) NOT NULL UNIQUE,
    directory VARCHAR(12) NOT NULL UNIQUE,
    post_limit INT NOT NULL DEFAULT 10,
    thread_limit INT NOT NULL DEFAULT 10,
    archive_limit INT NOT NULL DEFAULT 10,
    PRIMARY KEY(id)
);

INSERT INTO boards (name, directory, post_limit, thread_limit, archive_limit) VALUES ("Crypto", "c", 10, 10, 10);
INSERT INTO boards (name, directory, post_limit, thread_limit, archive_limit) VALUES ("Mathematics", "math", 10, 10, 10);
INSERT INTO boards (name, directory, post_limit, thread_limit, archive_limit) VALUES ("Minecraft", "mc", 10, 10, 10);
INSERT INTO boards (name, directory, post_limit, thread_limit, archive_limit) VALUES ("Movies", "mv", 10, 10, 10);
INSERT INTO boards (name, directory, post_limit, thread_limit, archive_limit) VALUES ("Random", "r", 10, 10, 10);
INSERT INTO boards (name, directory, post_limit, thread_limit, archive_limit) VALUES ("Technology", "tch", 10, 10, 10);
INSERT INTO boards (name, directory, post_limit, thread_limit, archive_limit) VALUES ("Video Games", "vc", 10, 10, 10);

CREATE TABLE threads (
    id INT NOT NULL AUTO_INCREMENT,
    board_id INT NOT NULL,
    time_updated TIMESTAMP,
    post_count INT NOT NULL,
    image_count INT NOT NULL,
    name VARCHAR(1024) NOT NULL,
    uploader_id INT NOT NULL,
    is_archived bool NOT NULL DEFAULT 0,
    FOREIGN KEY(board_id) REFERENCES boards(id),
    FOREIGN KEY(uploader_id) REFERENCES users(id),
    PRIMARY KEY(id)
);

INSERT INTO threads (board_id, time_updated, post_count, image_count, name, uploader_id, is_archived)
VALUES (5, "1970-01-01 00:00:01", 1, 1, "Ottman Empire Thread", 1, 0);
INSERT INTO threads (board_id, time_updated, post_count, image_count, name, uploader_id, is_archived)
VALUES (5, "1970-01-01 00:00:01", 1, 1, "How to remove flock material from car dashboard", 1, 0);

CREATE TABLE posts (
    id INT NOT NULL AUTO_INCREMENT,
    thread_id INT NOT NULL,
    uploader_id INT NOT NULL,
    file_id INT,
    content VARCHAR(8192),
    time_created TIMESTAMP,
    FOREIGN KEY(thread_id) REFERENCES threads(id),
    FOREIGN KEY(uploader_id) REFERENCES users(id),
    FOREIGN KEY(file_id) REFERENCES files(id),
    PRIMARY KEY(id)
);

INSERT INTO posts (thread_id, uploader_id, file_id, content, time_created)
VALUES (1, 1, 1, "What do you think of the Ottoman Empire?", NOW());
INSERT INTO posts (thread_id, uploader_id, file_id, content, time_created)
VALUES (2, 1, 2, "A friend of mine put flock on his car's dashboard and can't seem to get it off any of you know how to?", NOW());

CREATE TABLE post_replies (
    id INT NOT NULL AUTO_INCREMENT,
    parent_post_id INT NULL,
    reply_post_id INT NOT NULL,
    FOREIGN KEY(parent_post_id) REFERENCES posts(id),
    FOREIGN KEY(reply_post_id) REFERENCES posts(id),
    PRIMARY KEY(id)
);