USE three_leaf;

DELIMITER //
CREATE OR REPLACE PROCEDURE getBoardIdFromDirectory(
    IN board_directory VARCHAR(12),
    OUT board_id INT)
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
    IN board_directory VARCHAR(12))
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
    IN username VARCHAR(36),
    OUT user_id INT)
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
    IN board_directory VARCHAR(12))
BEGIN
    DECLARE board_id INT;
    DECLARE board_thread_limit INT;
    CALL getBoardIdFromDirectory(board_directory, board_id);
    SELECT boards.thread_limit FROM boards WHERE boards.id = board_id INTO board_thread_limit;
    SELECT
        threads.id,
        threads.post_count,
        threads.image_count,
        threads.name,
        threads.is_archived
    FROM
        threads
    WHERE
        board_id = threads.board_id
    ORDER BY
        threads.time_updated DESC
    LIMIT
        board_thread_limit;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE selectArchivedThreadsFromBoard(
    IN board_directory VARCHAR(12))
BEGIN
    DECLARE board_id INT;
    CALL getBoardIdFromDirectory(board_directory, board_id);
    DECLARE archive_limit INT;
    DECLARE thread_limit INT;
    SELECT boards.archive_limit, boards.thread_limit FROM boards WHERE boards.id = board_id INTO archive_limit, thread_limit;
    SELECT
        threads.id,
        threads.post_count,
        threads.image_count,
        threads.name,
        threads.is_archived
    FROM
        threads
    WHERE
        board_id = threads.board_id
    ORDER BY
        threads.time_updated DESC
    LIMIT # This is awful but MySQL suggests we use a cap so I would rather use archive_limit than max INT.
          # Threads should not live very long anyways so the archive will not grow large.
          # If not, then we just do not show old archived threads that will be pruned.
        archive_limit OFFSET thread_limit;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE selectThreadById(
    IN thread_id INT)
BEGIN
    SELECT
        threads.id,
        threads.post_count,
        threads.image_count,
        threads.name,
        threads.is_archived
    FROM
        threads
    WHERE
        threads.id = thread_id
    LIMIT 1;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE selectPostsFromThread(
    IN thread_id VARCHAR(36))
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
    IN post_id INT)
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
    IN thread_id VARCHAR(36))
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
    IN board_directory VARCHAR(12),
    IN thread_id INT,
    IN content VARCHAR(8192),
    IN uploader_name VARCHAR(36),
    IN file_id INT)
proc_label: BEGIN
    DECLARE board_id INT;
    DECLARE board_post_limit INT;
    DECLARE uploader_id INT;
    DECLARE post_count INT;

    # If the thread is archived, we do not allow posting.
    IF (SELECT threads.is_archived FROM threads WHERE thread_id = threads.id) = 1 THEN
        LEAVE proc_label;
    END IF;

    CALL getBoardIdFromDirectory(board_directory, board_id);
    CALL getUserIdFromUsername(uploader_name, uploader_id);

    SELECT boards.post_limit FROM boards WHERE boards.id = board_id INTO board_post_limit;

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

    # We stop bumping the thread to the front if there are enough replies.
    IF post_count < board_post_limit THEN
        UPDATE threads SET threads.time_updated = NOW() WHERE threads.id = thread_id;
    END IF;

    SELECT LAST_INSERT_ID();
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE createThread(
    IN board_directory VARCHAR(12),
    IN name VARCHAR(1024),
    IN content VARCHAR(8192),
    IN uploader_name VARCHAR(36),
    IN file_id INT)
BEGIN
    DECLARE board_id INT;
    DECLARE board_thread_limit INT;
    DECLARE thread_id INT;
    DECLARE uploader_id INT;
    SELECT 
        users.id
    FROM 
        users
    WHERE
        LOWER(users.username) = LOWER(uploader_name)
    INTO 
        uploader_id;
    CALL getBoardIdFromDirectory(board_directory, board_id);
    INSERT INTO threads (board_id, time_updated, post_count, image_count, name, uploader_id)
    VALUES (board_id, NOW(), 1, 1, name, uploader_id);
    SELECT LAST_INSERT_ID() INTO thread_id;
    CALL createPost(board_directory, thread_id, content, uploader_name, file_id);
    # Archive the sliding thread if it exists. This is the thread that slid off the page.
    SELECT boards.thread_limit FROM boards WHERE boards.id = board_id INTO board_thread_limit;
    UPDATE threads SET threads.is_archived = 1 WHERE threads.id = (SELECT threads.id FROM threads LIMIT 1 OFFSET board_thread_limit);
    CALL selectThreadById(thread_id);
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE findPasswordEntryForUsername(
    IN username VARCHAR(36))
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
    IN username VARCHAR(36),
    IN password VARCHAR(255),
    OUT errorMessage VARCHAR(255))
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
    IN file_name VARCHAR(40),
    IN uploader_name VARCHAR(36))
BEGIN
    DECLARE uploader_id INT;
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
    IN parent_post_id INT)
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
    IN parent_post_id INT,
    IN reply_post_id INT)
BEGIN
    INSERT INTO post_replies (parent_post_id, reply_post_id) 
    VALUES (parent_post_id, reply_post_id);
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE fetchActivePostsFromUser(
    IN username VARCHAR(36))
BEGIN
    DECLARE user_id INT;
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

DELIMITER //
CREATE OR REPLACE PROCEDURE deleteThread(
    IN thread_id INT,
    IN username VARCHAR(36),
    OUT did_delete bool)
BEGIN
    DECLARE user_id INT;
    DECLARE user_role INT;
    DECLARE thread_owner_id INT;
    SET did_delete = 0;
    SELECT
        users.id,
        users.role
    FROM
        users
    WHERE
        LOWER(users.username) = LOWER(username)
    INTO
        user_id,
        user_role;
    SELECT
        threads.uploader_id
    FROM
        threads
    WHERE
        threads.id = thread_id
    INTO
        thread_owner_id;
    IF user_id = thread_owner_id OR user_role = 1 THEN
        DELETE FROM threads WHERE threads.id = thread_id;
        DELETE FROM posts WHERE posts.thread_id = thread_id;
        SET did_delete = 1;
    END IF;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE deletePost(
    IN post_id INT,
    IN username VARCHAR(36),
    OUT did_delete bool)
BEGIN
    DECLARE user_id INT;
    DECLARE user_role INT;
    DECLARE post_owner_id INT;
    SET did_delete = 0;
    SELECT
        users.id,
        users.role
    FROM
        users
    WHERE
        LOWER(users.username) = LOWER(username)
    INTO
        user_id,
        user_role;
    SELECT
        posts.uploader_id
    FROM
        posts
    WHERE
        posts.id = post_id
    INTO
        post_owner_id;
    IF user_id = post_owner_id OR user_role = 1 THEN
        DELETE FROM posts WHERE posts.post_id = post_id;
        SET did_delete = 1;
    END IF;
END //
DELIMITER ;