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

CREATE TABLE users (
    id int NOT NULL AUTO_INCREMENT,
    password_id int NOT NULL,
    username varchar(36) NOT NULL UNIQUE,
    FOREIGN KEY(password_id) REFERENCES passwords(id),
    PRIMARY KEY(id)
);

CREATE TABLE files (
    id int NOT NULL AUTO_INCREMENT,
    uploader_id int NOT NULL,
    file_name varchar(36) NOT NULL UNIQUE,
    FOREIGN KEY(uploader_id) REFERENCES users(id),
    PRIMARY KEY(id)
);

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

INSERT INTO posts (thread_id, uploader_id, )

CREATE TABLE post_replies (
    id int NOT NULL AUTO_INCREMENT,
    root_post_id int NOT NULL,
    reply_post_id int NOT NULL,
    FOREIGN KEY(root_post_id) REFERENCES posts(id),
    FOREIGN KEY(reply_post_id) REFERENCES posts(id),
    PRIMARY KEY(id)
);