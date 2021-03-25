USE dcaron;

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
    CALL getBoardIdFromDirectory(board_directory, board_id);
    SELECT
        threads.id,
        threads.post_count,
        threads.image_count,
        threads.name,
        threads.is_archived
    FROM
        threads
    WHERE
        board_id = threads.board_id AND threads.is_archived = 0
    ORDER BY
        threads.time_updated DESC;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE selectArchivedThreadsFromBoard(
    IN board_directory VARCHAR(12))
BEGIN
    DECLARE board_id INT;
    DECLARE archive_limit INT;
    DECLARE thread_limit INT;
    CALL getBoardIdFromDirectory(board_directory, board_id);
    SELECT 
        boards.archive_limit, boards.thread_limit 
    INTO 
        archive_limit, thread_limit 
    FROM 
        boards 
    WHERE 
        boards.id = board_id;
    SELECT
        threads.id,
        threads.post_count,
        threads.image_count,
        threads.name,
        threads.is_archived
    FROM
        threads
    WHERE
        board_id = threads.board_id AND threads.is_archived = 1
    ORDER BY
        threads.time_updated DESC;
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
    VALUES (thread_id, uploader_id, file_id, content, NOW(6));

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
        UPDATE threads SET threads.time_updated = NOW(6) WHERE threads.id = thread_id;
    END IF;

    SELECT LAST_INSERT_ID();
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE createThread(
    IN board_directory VARCHAR(12),
    IN name VARCHAR(64),
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
    VALUES (board_id, NOW(6), 0, 0, name, uploader_id);
    SELECT LAST_INSERT_ID() INTO thread_id;
    CALL createPost(board_directory, thread_id, content, uploader_name, file_id);
    # Archive the sliding thread if it exists. This is the thread that slid off the page.
    SELECT boards.thread_limit FROM boards WHERE boards.id = board_id INTO board_thread_limit;
    UPDATE threads SET threads.is_archived = 1 WHERE threads.id = (SELECT threads.id FROM threads WHERE threads.board_id = board_id ORDER BY threads.time_updated DESC LIMIT 1 OFFSET board_thread_limit);
    CALL pruneOldThreads(board_id);
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
    INSERT INTO users (password_id, username, role) VALUES (LAST_INSERT_ID(), username, 0);
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
    # Is the user the creator of the thread or a moderator?
    IF user_id = thread_owner_id OR user_role = 1 THEN
        CALL deletePostsFromThread(thread_id);
        DELETE FROM threads WHERE threads.id = thread_id;
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
    DECLARE post_file_id INT;
    DECLARE thread_id INT;
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
        DELETE FROM post_replies WHERE post_replies.parent_post_id = post_id OR post_replies.reply_post_id = post_id;
        SELECT posts.file_id, posts.thread_id FROM posts WHERE posts.id = post_id INTO post_file_id, thread_id;
        DELETE FROM posts WHERE posts.id = post_id;
        DELETE FROM files WHERE post_file_id IS NOT NULL AND files.id = post_file_id;
        IF post_file_id IS NOT NULL THEN
            UPDATE threads SET threads.post_count = threads.post_count - 1, threads.image_count = threads.image_count - 1 WHERE threads.id = thread_id;
        ELSE
            UPDATE threads SET threads.post_count = threads.post_count - 1 WHERE threads.id = thread_id;
        END IF;
        SET did_delete = 1;
    END IF;
END //
DELIMITER ;

# Assumes this is called by the thread owner or moderator.
DELIMITER //
CREATE OR REPLACE PROCEDURE deletePostsFromThread(
    IN thread_id INT)
BEGIN
    DECLARE done BOOL DEFAULT FALSE;
    DECLARE post_id INT;
    DECLARE did_delete BOOL;
    DECLARE cur CURSOR FOR (
        SELECT 
            posts.id
        FROM
            posts
        WHERE 
            posts.thread_id = thread_id
    );
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    OPEN cur;
    post_del_loop: LOOP
        FETCH cur INTO post_id;
        IF done = TRUE THEN
            LEAVE post_del_loop;
        END IF;
        # Daltondalt is the root user.
        CALL deletePost(post_id, "Daltondalt", did_delete);
    END LOOP post_del_loop;
    CLOSE cur;
END //
DELIMITER ;

# It really tries to prune a single thread. Should be called when a new thread is created.
DELIMITER //
CREATE OR REPLACE PROCEDURE pruneOldThreads(
    IN board_id INT)
BEGIN
    DECLARE prune_offset INT;
    DECLARE prune_thread_id INT;
    SET prune_thread_id = 0;
    SELECT (boards.thread_limit + boards.archive_limit) FROM boards WHERE boards.id = board_id LIMIT 1 INTO prune_offset;
    SELECT
        threads.id
    INTO
        prune_thread_id
    FROM
        threads
    WHERE
        threads.board_id = board_id
    LIMIT
        1 OFFSET prune_offset;
    IF prune_thread_id != 0 THEN
        DELETE FROM threads WHERE threads.id = prune_thread_id;
    END IF;
END //
DELIMITER ;

# It just returns their role, but we could have more data for users.
DELIMITER //
CREATE OR REPLACE PROCEDURE getUser(
    IN username VARCHAR(36))
BEGIN
    SELECT
        users.id,
        users.role
    FROM
        users
    WHERE
        LOWER(username) = LOWER(users.username);
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE getFileRecordFromPostID(
    IN post_id INT)
BEGIN
    SELECT
        files.file_name
    FROM
        files
    LEFT JOIN
        posts ON posts.id = post_id
    WHERE
        posts.file_id = files.id
    LIMIT
        1;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE fetchFileRecordsFromThread(
    IN thread_id INT)
BEGIN
    SELECT
        files.id,
        files.file_name
    FROM
        threads
    LEFT JOIN
        posts ON posts.thread_id = thread_id
    LEFT JOIN
        files ON posts.file_id = files.id
    WHERE
        threads.id = thread_id;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE deleteFileRecord(
    IN file_id INT)
BEGIN
    DELETE FROM files WHERE file_id = files.id;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE createReport(
    IN post_id INT,
    OUT did_create INT)
BEGIN
    DECLARE thread_id INT;
    DECLARE board_dir VARCHAR(12);
    SET did_create = 0;
    IF (SELECT reports.post_id FROM reports WHERE reports.post_id = post_id) IS NULL THEN 
        SELECT posts.thread_id FROM posts WHERE posts.id = post_id INTO thread_id;
        SELECT boards.directory FROM threads LEFT JOIN boards ON boards.id = threads.board_id WHERE threads.id = thread_id INTO board_dir; 
        INSERT INTO reports (post_id, thread_id, board_dir) VALUES (post_id, thread_id, board_dir);
        SET did_create = 1;
    END IF;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE deleteReport(
    IN report_id INT)
BEGIN
    DELETE FROM reports WHERE reports.id = report_id;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE insertIpAccess(
    IN ip_addr VARCHAR(36),
    IN operation VARCHAR(64)
)
BEGIN
    IF (SELECT ip_accesses.ip_addr FROM ip_accesses WHERE ip_accesses.ip_addr = ip_addr AND ip_accesses.operation = operation) IS NULL THEN
        INSERT INTO ip_accesses (ip_addr, operation, access_time) VALUES (ip_addr, operation, NOW(6));
    END IF;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE testIpAccess(
    IN ip_addr VARCHAR(36),
    IN operation VARCHAR(64),
    IN wait_time_seconds INT,
    OUT allow_access BOOL
)
BEGIN
    DECLARE access_time TIMESTAMP(6);
    SET access_time = NULL;
    SET allow_access = 0;
    SELECT 
        ip_accesses.access_time
    FROM
        ip_accesses
    WHERE
        ip_addr = ip_accesses.ip_addr AND operation = ip_accesses.operation
    INTO
        access_time;
    IF access_time IS NULL OR TIMESTAMPADD(SECOND, wait_time_seconds, access_time) < NOW(6) THEN 
        DELETE FROM ip_accesses WHERE ip_accesses.ip_addr = ip_addr AND ip_accesses.operation = operation;
        SET allow_access = 1;
    END IF;
END //
DELIMITER ;

DELIMITER //
CREATE OR REPLACE PROCEDURE pullThreadsForHomepage(
    IN amount INT
)
BEGIN
    SELECT
        threads.id,
        threads.post_count,
        threads.image_count,
        threads.name,
        users.username,
        boards.directory
    FROM
        threads
    LEFT JOIN users
        ON users.id = threads.uploader_id
    LEFT JOIN boards
        ON boards.id = threads.board_id
    WHERE
        threads.is_archived = 0
    ORDER BY 
        threads.time_updated DESC
    LIMIT
        amount;
END //
DELIMITER ;