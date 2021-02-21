USE three_leaf;

DROP TABLE IF EXISTS post_replies;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS threads;
DROP TABLE IF EXISTS boards;
DROP TABLE IF EXISTS files;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS passwords;

CREATE TABLE passwords (
    id int NOT NULL AUTO_INCREMENT,
    password varchar(255) NOT NULL,
    PRIMARY KEY(id)
);

INSERT INTO passwords (password) VALUES ("bruh");

CREATE TABLE users (
    id int NOT NULL AUTO_INCREMENT,
    password_id int NOT NULL,
    username varchar(36) NOT NULL UNIQUE,
    FOREIGN KEY(password_id) REFERENCES passwords(id),
    PRIMARY KEY(id)
);

INSERT INTO users (password_id, username) VALUES (1, "Daltondalt");

CREATE TABLE files (
    id int NOT NULL AUTO_INCREMENT,
    uploader_id int NOT NULL,
    file_name varchar(40) NOT NULL UNIQUE,
    FOREIGN KEY(uploader_id) REFERENCES users(id),
    PRIMARY KEY(id)
);

INSERT INTO files (uploader_id, file_name) VALUES (1, "fdc9a303-b8e0-4c01-9455-459c7334dec7.jpg");
INSERT INTO files (uploader_id, file_name) VALUES (1, "844ca79d-5aab-438a-8abe-50edc6c8a947.jpg");

CREATE TABLE boards (
    id int NOT NULL AUTO_INCREMENT,
    name varchar(36) NOT NULL UNIQUE,
    directory varchar(12) NOT NULL UNIQUE,
    PRIMARY KEY(id)
);

INSERT INTO boards (name, directory) VALUES ("Movies", "mv");
INSERT INTO boards (name, directory) VALUES ("Video Games", "vc");
INSERT INTO boards (name, directory) VALUES ("Technology", "tch");
INSERT INTO boards (name, directory) VALUES ("Random", "r");
INSERT INTO boards (name, directory) VALUES ("Minecraft", "mc");
INSERT INTO boards (name, directory) VALUES ("Crypto", "c");
INSERT INTO boards (name, directory) VALUES ("Mathematics", "math");

CREATE TABLE threads (
    id int NOT NULL AUTO_INCREMENT,
    board_id int NOT NULL,
    time_updated TIMESTAMP,
    post_count int NOT NULL,
    image_count int NOT NULL,
    name varchar(1024) NOT NULL,
    FOREIGN KEY(board_id) REFERENCES boards(id),
    PRIMARY KEY(id)
);

INSERT INTO threads (board_id, time_updated, post_count, image_count, name)
VALUES (4, "1970-01-01 00:00:01", 1, 1, "Ottman Empire Thread");
INSERT INTO threads (board_id, time_updated, post_count, image_count, name)
VALUES (4, "1970-01-01 00:00:01", 1, 1, "How to remove flock material from car dashboard");

CREATE TABLE posts (
    id int NOT NULL AUTO_INCREMENT,
    thread_id int NOT NULL,
    uploader_id int NOT NULL,
    file_id int,
    content varchar(8192),
    time_created TIMESTAMP,
    FOREIGN KEY(thread_id) REFERENCES threads(id),
    FOREIGN KEY(uploader_id) REFERENCES users(id),
    FOREIGN KEY(file_id) REFERENCES files(id),
    PRIMARY KEY(id)
);

INSERT INTO posts (thread_id, uploader_id, file_id, content, time_created)
VALUES (1, 1, 1, "What do you think of the Ottoman Empire?", "1970-01-01 00:00:01");
INSERT INTO posts (thread_id, uploader_id, file_id, content, time_created)
VALUES (2, 1, 2, "    A friend of mine put flock on his car's dashboard and can't seem to get it off any of you know how to?", "1980-01-01 00:00:01");

CREATE TABLE post_replies (
    id int NOT NULL AUTO_INCREMENT,
    parent_post_id int NULL,
    reply_post_id int NOT NULL,
    FOREIGN KEY(parent_post_id) REFERENCES posts(id),
    FOREIGN KEY(reply_post_id) REFERENCES posts(id),
    PRIMARY KEY(id)
);