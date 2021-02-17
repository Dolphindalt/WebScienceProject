USE three_leaf;

DELIMITER //
CREATE OR REPLACE PROCEDURE getBoardIdFromDirectory(
    IN board_directory varchar(12),
    OUT board_id int)
BEGIN
    SELECT
        boards.id
    FROM 
        boards
    WHERE
        boards.directory = board_directory
    INTO
        board_id;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE getBoardFromDirectory(
    IN board_directory varchar(12))
BEGIN
    SELECT
        boards.directory,
        boards.name
    FROM 
        boards
    WHERE
        boards.directory = board_directory;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE getUserIdFromUsername(
    IN username varchar(36),
    OUT user_id int)
BEGIN
    SELECT
        users.id
    WHERE
        LOWER(users.username) = LOWER(username)
    LIMIT 
        1
    INTO 
        user_id;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE selectThreadsFromBoard(
    IN board_directory varchar(12))
BEGIN
    DECLARE board_id INT;
    CALL getBoardIdFromDirectory(board_directory, board_id);
    SELECT
        threads.id,
        threads.post_count,
        threads.image_count,
        threads.name
    FROM
        threads
    WHERE
        board_id = threads.board_id
    ORDER BY
        threads.time_updated DESC;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE selectThreadById(
    IN thread_id int)
BEGIN
    SELECT
        threads.id,
        threads.post_count,
        threads.image_count,
        threads.name
    FROM
        threads
    WHERE
        threads.id = thread_id
    LIMIT 1;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE selectPostsFromThread(
    IN thread_id varchar(36))
BEGIN
    SELECT
        posts.id,
        posts.content,
        posts.time_created,
        users.username,
        files.file_name
    FROM
        posts
    LEFT JOIN
        users ON users.id = posts.uploader_id
    LEFT JOIN
        files ON posts.file_id = files.id
    WHERE 
        thread_id = posts.thread_id
    ORDER BY
        posts.time_created DESC;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE selectRootPostFromThread(
    IN thread_id varchar(36))
BEGIN
    SELECT
        posts.id,
        posts.content,
        posts.time_created,
        users.username,
        files.file_name
    FROM
        posts
    LEFT JOIN
        users ON users.id = posts.uploader_id
    LEFT JOIN
        files ON posts.file_id = files.id
    WHERE 
        thread_id = posts.thread_id
    ORDER BY
        posts.time_created DESC
    LIMIT 1;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE createPost(
    IN board_directory varchar(12),
    IN thread_id int,
    IN content varchar(8192),
    IN uploader_id int,
    IN file_id int)
BEGIN
    DECLARE board_id int;
    CALL getBoardIdFromDirectory(board_directory, board_id);
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE createThread(
    IN board_directory varchar(12),
    IN name varchar(1024),
    IN content varchar(8192),
    IN uploader_name varchar(36),
    IN file_id int)
BEGIN
    DECLARE board_id int;
    DECLARE thread_id int;
    DECLARE uploader_id int;
    CALL getBoardIdFromDirectory(board_directory, board_id);
    CALL getUserIdFromUsername(uploader_name, uploader_id);
    INSERT INTO threads (board_id, time_updated, post_count, image_count, name)
    VALUES (board_id, NOW(), 1, 1, name);
    SELECT LAST_INSERT_ID() INTO thread_id;
    CALL createPost(board_directory, thread_id, content, uploader_id, file_id);
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE findPasswordEntryForUsername(
    IN username varchar(36))
BEGIN
    SELECT
        passwords.password
    FROM 
        users
    LEFT JOIN passwords ON
        passwords.id = users.password_id
    WHERE
        users.username = username
    LIMIT 1;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE tryCreateUser(
    IN username varchar(36),
    IN password varchar(255),
    OUT errorMessage varchar(255))
proc_label: BEGIN
    IF (SELECT EXISTS (SELECT
        users.username
    FROM
        users
    WHERE
        LOWER(users.username) = LOWER(username))) THEN
        SET errorMessage = 'The username is already in use.';
        LEAVE proc_label;
    END IF;
    INSERT INTO passwords (password) VALUES (password);
    INSERT INTO users (password_id, username) VALUES (LAST_INSERT_ID(), username);
    SET errorMessage = '';
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE insertFileRecord(
    IN file_name varchar(40),
    IN uploader_name varchar(36))
BEGIN
    DECLARE uploader_id int;
    SELECT 
        users.id
    WHERE
        users.username = uploader_name
    INTO uploader_id;
    INSERT INTO files (uploader_id, file_name) 
    VALUES (uploader_id, file_name);
    SELECT LAST_INSERT_ID();
END //
DELIMITER ;