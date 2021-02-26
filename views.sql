USE three_leaf;

CREATE OR REPLACE VIEW selectBoards
AS
SELECT
    name,
    directory
FROM
    boards;

CREATE OR REPLACE VIEW selectPopularThreads
AS
SELECT DISTINCT
    boards.name AS board_name,
    boards.directory,
    threads.name AS thread_name,
    posts.id,
    posts.file_id,
    posts.content
FROM
    posts
LEFT JOIN
    threads ON threads.id = posts.thread_id
LEFT JOIN
    boards ON boards.id = threads.id
ORDER BY
    threads.post_count DESC
LIMIT 6;

CREATE OR REPLACE VIEW selectReports
AS
SELECT
    reports.id,
    reports.post_id,
    reports.thread_id,
    reports.board_dir
FROM
    reports;