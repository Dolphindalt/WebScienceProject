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
    FROM 
        users
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
        posts.time_created ASC;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE selectPostIDsByPostID(
    IN post_id int)
BEGIN
    SELECT
        posts.id,
        posts.thread_id,
        boards.directory
    FROM
        posts
    LEFT JOIN
        threads ON threads.id = posts.thread_id
    LEFT JOIN 
        boards ON threads.board_id = boards.id
    WHERE
        posts.id = post_id
    LIMIT 1;
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
        posts.time_created ASC
    LIMIT 1;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE createPost(
    IN board_directory varchar(12),
    IN thread_id int,
    IN content varchar(8192),
    IN uploader_name varchar(36),
    IN file_id int)
BEGIN
    DECLARE board_id int;
    DECLARE uploader_id int;
    DECLARE post_count int;

    CALL getBoardIdFromDirectory(board_directory, board_id);
    CALL getUserIdFromUsername(uploader_name, uploader_id);

    # Insertion of the post data.
    INSERT INTO posts (thread_id, uploader_id, file_id, content, time_created) 
    VALUES (thread_id, uploader_id, file_id, content, NOW());

    # Update the post counter.
    UPDATE threads SET threads.post_count = threads.post_count + 1 WHERE threads.id = thread_id;

    # Update the image counter.
    IF file_id IS NOT NULL THEN 
        UPDATE threads SET threads.image_count = threads.image_count + 1 WHERE threads.id = thread_id;
    END IF;
    
    SELECT
        threads.post_count
    FROM
        threads
    WHERE
        threads.id = thread_id
    LIMIT
        1
    INTO
        post_count;

    # We stop bumping the thread to the front if there are 10 replies.
    IF post_count < 10 THEN
        UPDATE threads SET threads.time_updated = NOW() WHERE threads.id = thread_id;
    END IF;

    SELECT LAST_INSERT_ID();
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
    CALL getBoardIdFromDirectory(board_directory, board_id);
    INSERT INTO threads (board_id, time_updated, post_count, image_count, name)
    VALUES (board_id, NOW(), 1, 1, name);
    SELECT LAST_INSERT_ID() INTO thread_id;
    CALL createPost(board_directory, thread_id, content, uploader_name, file_id);
    CALL selectThreadById(thread_id);
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
    FROM 
        users
    WHERE
        LOWER(users.username) = LOWER(uploader_name)
    INTO 
        uploader_id;
    INSERT INTO files (uploader_id, file_name) 
    VALUES (uploader_id, file_name);
    SELECT
        files.id
    FROM
        files
    WHERE
        file_name = files.file_name and uploader_id = uploader_id
    LIMIT
        1;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE getRepliesToPost(
    IN parent_post_id int)
BEGIN
    SELECT
        post_replies.reply_post_id
    FROM
        post_replies
    WHERE 
        post_replies.parent_post_id = parent_post_id;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE createPostReplyRecord(
    IN parent_post_id int,
    IN reply_post_id int)
BEGIN
    INSERT INTO post_replies (parent_post_id, reply_post_id) 
    VALUES (parent_post_id, reply_post_id);
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE fetchActivePostsFromUser(
    IN username varchar(36))
BEGIN
    DECLARE user_id int;
    SELECT 
        users.id
    FROM 
        users
    WHERE
        LOWER(users.username) = LOWER(username)
    INTO 
        user_id;
    SELECT DISTINCT
        posts.id AS post_id,
        posts.content,
        posts.time_created,
        threads.name,
        threads.id AS thread_id,
        boards.directory,
        boards.name AS board_name
    FROM
        posts
    LEFT JOIN
        threads ON posts.thread_id = threads.id
    LEFT JOIN
        boards ON boards.id = threads.board_id
    WHERE
        posts.uploader_id = user_id
    ORDER BY
        posts.time_created DESC;
END //
DELIMITER ;