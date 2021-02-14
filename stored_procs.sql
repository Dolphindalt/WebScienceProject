USE three_leaf;

DELIMITER //
CREATE OR REPLACE PROCEDURE getBoardFromDirectory(
    IN board_directory varchar(12))
BEGIN
    SELECT
        boards.id
    FROM 
        boards
    WHERE
        boards.directory = board_directory;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE selectThreadsFromBoard(
    IN board_directory varchar(12))
BEGIN
    DECLARE board_id INT;
    SET board_id = CALL getBoardFromDirectory(board_directory);
    SELECT
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
CREATE OR REPLACE PROCEDURE selectPostsFromThread(
    IN thread_id varchar(36))
BEGIN
    SELECT
        posts.id,
        posts.content,
        posts.time_created,
        users.name,
        files.file_name
    FROM
        posts
    LEFT JOIN
        users ON users.id = posts.uploader_id
    LEFT JOIN
        files ON posts.file_id = files.id
    WHERE 
        thread_id = threads.id;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE createPost(
    IN board_directory varchar(12),
    IN thread_id int,
    IN content varchar(8192),
    IN uploader_id int,
    IN file_id int)
BEGIN
    DECLARE board_id int;
    SET board_id = CALL getBoardFromDirectory(board_directory);
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE createThread(
    IN board_directory varchar(12),
    IN name varchar(1024),
    IN content varchar(8192),
    IN uploader_id int,
    IN file_id int)
BEGIN
    DECLARE board_id int;
    DECLARE thread_id int;
    SET board_id = CALL getBoardFromDirectory(board_directory);
    INSERT INTO threads (board_id, time_updated, post_count, image_count, name)
    VALUES (board_id, NOW(), 1, 1, name);
    SET thread_id = SELECT LAST_INSERT_ID();
    CALL createPost(board_directory, thread_id, content, uploader_id, file_id);
END //
DELIMITER ;