<?php

namespace Dalton\ThreeLeaf\Controllers;

require_once ROOT_PATH.'framework/Controller.php';
require_once ROOT_PATH.'framework/View.php';
require_once ROOT_PATH.'3leaf/Models/ThreadModel.php';
require_once ROOT_PATH.'3leaf/Controllers/FileController.php';
require_once ROOT_PATH.'3leaf/Controllers/ThreadPageController.php';
require_once ROOT_PATH.'3leaf/Controllers/BoardPageController.php';
require_once ROOT_PATH.'3leaf/Models/BoardModel.php';

use Dalton\Framework\View;
use Dalton\ThreeLeaf\Models\ThreadModel;
use Dalton\ThreeLeaf\Controllers\FileUpload;
use Dalton\Framework\ControllerBase;
use Dalton\ThreeLeaf\Models\BoardModel;
use Dalton\ThreeLeaf\Controllers\ThreadPage;
use Dalton\ThreeLeaf\Controllers\BoardPage;
use Dalton\ThreeLeaf\Models\FileModel;
use Dalton\ThreeLeaf\Models\PostModel;

class Threads extends ControllerBase {

    public function listFromBoardTask() {
        $boardThreads = ThreadModel::getThreads($this->params['board']);
        echo json_encode($boardThreads);
    }

	public function deletePostTask() {
        if (!array_key_exists('post_id', $this->params)) {
            $this->pageNotFound();
        }

        if (!isset($_SESSION[LOGGED_IN])) {
            http_response_code(401);
            die();
        }

        $post_id = $this->params['post_id'];

        $file_results = FileModel::getFileRecordFromPostID($post_id);

        $result = PostModel::deletePost($post_id, $_SESSION[USERNAME]);
        echo $result;
        if ($result) {
            if ($file_results != null) {
                $file_name = $file_results['file_name'];
                echo $file_name;
                unlink(ROOT_PATH . 'public/post_images/' . $file_name);
            }
            http_response_code(204);
        } else {
            http_response_code(401);
        }
    }

	public function deleteThreadTask() {
        if (!array_key_exists('thread_id', $this->params)) {
            $this->pageNotFound();
        }

        if (!isset($_SESSION[LOGGED_IN])) {
            http_response_code(401);
            die();
        }

        $thread_id = $this->params['thread_id'];

        $file_records = FileModel::getFileRecordsFromThreadID($thread_id);

        $result = ThreadModel::deleteThread($thread_id, $_SESSION[USERNAME]);

        if ($result) {
            if ($file_records != null) {
                foreach ($file_records as $file) {
                    unlink(ROOT_PATH . 'public/post_images/' . $file['file_name']);
                    FileModel::deleteFileRecord($file['id']);
                }
            }
            http_response_code(204);
        } else {
            http_response_code(401);
        }
    }

    public function createThreadTask() {
        // Honey pot tactic to prevent bot spam.
        if(!empty($_POST['website'])) die();

        if (!array_key_exists('dir', $this->params)) {
            $this->pageNotFound();
        }

        $board_dir = $this->params['dir'];

        if (!BoardModel::isBoardDirectoryValid($board_dir)) {
            http_response_code(404);
            View::render('MessageView.php', ['header' => 'Board not found', 'content' => 'Invalid board directory.']);
            die();
        }

        if (!isset($_SESSION[LOGGED_IN])) {
            $this->showErrorOnBoardCatalog($board_dir, 'You need to log in to make a post.');
        }

        $thread_name = $_POST['name'];
        $content = $_POST['comment'];

        if (empty($thread_name)) {
            $this->showErrorOnBoardCatalog($board_dir, 'Thread name required.');
        }

        if (empty($content)) {
            $this->showErrorOnBoardCatalog($board_dir, 'Thread comment required.');
        }

        $file_controller = new FileUpload();
        $results = $file_controller->tryUploadFile($_SESSION[USERNAME]);
        if (array_key_exists('error', $results)) {
            $this->showErrorOnBoardCatalog($board_dir, $results['error']);
        }

        $file_id = $results['id'];
        $content = $this->strip_html($content);
        $content = nl2br($content);
        $content = $this->strip_slashes_and_non_spaces($content);
        $content = $this->addGreentext($content);
        $thread_name = $this->strip_html_and_slashes_and_non_spaces($thread_name);
        $thread = ThreadModel::createThread($board_dir, $thread_name, $content, $_SESSION[USERNAME], $file_id);
        $post_id = $thread['post_id']; // Only works for createThread.

        $this->createRepliesOnPost($post_id, $content);

        ?>
            <script>
                setCookie('modal-status', 'closed');
            </script>
        <?php
        // We really need to redirect so that the query string changes and does not mess up posting.
        header('Location: index.php?board/dir=' . $board_dir . '/thread=' . $thread['id']);
    }

    public function createPostOnThreadTask() {
        // Honey pot tactic to prevent bot spam.
        if(!empty($_POST['website'])) die();
        
        $board_dir = $this->params['dir'];
        $thread_id = $this->params['thread'];
        $content = $_POST['comment'];

        if (!BoardModel::isBoardDirectoryValid($board_dir)) {
            http_response_code(404);
            View::render('MessageView.php', ['header' => 'Board not found', 'content' => 'Invalid board directory.']);
            die();
        }

        $thread = ThreadModel::getThread($thread_id);
        if (!$thread) {
            http_response_code(404);
            View::render('MessageView.php', ['header' => 'Thread not found', 'content' => 'Invalid thread id.']);
            die();
        }

        if (!isset($_SESSION[LOGGED_IN])) {
            $this->showErrorOnThreadPage($board_dir, $thread_id, 'You need to log in to make a post.');
        }

        if ($thread['is_archived']) {
            $this->showErrorOnThreadPage($board_dir, $thread_id, 'You cannot post on an archived thread.');
        }

        if (empty($content)) {
            $this->showErrorOnThreadPage($board_dir, $thread_id, 'Post comment required.');
        }

        $file_id = null;
        if (!empty($_FILES)) {
            $file_controller = new FileUpload();
            $results = $file_controller->tryUploadOptionalImage($_SESSION[USERNAME]);
            if (array_key_exists('error', $results) && $results['error'] != "") {
                $this->showErrorOnThreadPage($board_dir, $thread_id, $results['error']);
            }
            $file_id = $results['id'];
        }

        $content = $this->strip_html($content);
        $content = nl2br($content);
        $content = $this->strip_slashes_and_non_spaces($content);
        $content = $this->addGreentext($content);

        ?>
            <script>
                setCookie('modal-status', 'closed');
            </script>
        <?php

        $post_id = PostModel::createPostInThread($board_dir, $thread_id, $content, $_SESSION[USERNAME], $file_id);

        // Create reply records for posts replied to in this new post.
        $this->createRepliesOnPost($post_id, $content);

        header('Location: index.php?board/dir=' . $board_dir . '/thread=' . $thread_id . '#p' . $post_id);
    }

    private function createRepliesOnPost($post_id, $content) {
        $matches = [];
		preg_match_all('/(&gt;&gt;)([0-9]+)/', $content, $matches);
		$matches = array_unique($matches, SORT_REGULAR);
        if (!empty($matches) && array_key_exists(2, $matches)) {
            foreach ($matches[2] as $digits) {
                PostModel::addReplyToPost($digits, $post_id);
            }
        }
    }

    private function addGreentext($content) {
        $new_content = preg_replace('/^(&gt;){1}[^&\n\r<]*(<br>)?(<br\/>)?\n?\r?/m', '<p style="color:#42a357;">\0</p>', $content);
        return $new_content;
    }

    private function showErrorOnBoardCatalog($dir, $error) {
        $board_page_controller = new BoardPage([]);
        http_response_code(400);
        $board_page_controller->showBoardCatalogWithError($dir, $error);
        die();
    }

    private function showErrorOnThreadPage($dir, $thread_id, $error) {
        $thread_page_controller = new ThreadPage([]);
        http_response_code(400);
        $thread_page_controller->showThreadPageWithError($dir, $thread_id, $error);
        die();
    }

}

?>
