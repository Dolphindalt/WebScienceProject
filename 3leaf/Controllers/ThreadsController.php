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
use Dalton\ThreeLeaf\Models\PostModel;

class Threads extends ControllerBase {

    public function listFromBoardTask() {
        $boardThreads = ThreadModel::getThreads($this->params['board']);
        echo json_encode($boardThreads);
    }

    public function createThreadTask() {
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
        $thread_name = $this->strip_html_and_slashes_and_non_spaces($thread_name);
        $thread = ThreadModel::createThread($board_dir, $thread_name, $content, $_SESSION[USERNAME], $file_id);

        $thread_page_controller = new ThreadPage([]);
        ?>
            <script>
                setCookie('modal-status', 'closed');
            </script>
        <?php
        $thread_page_controller->showThreadPageWithError($board_dir, $thread['id'], null);
    }

    public function createPostOnThreadTask() {
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

        ?>
            <script>
                setCookie('modal-status', 'closed');
            </script>
        <?php

        $post_id = PostModel::createPostInThread($board_dir, $thread_id, $content, $_SESSION[USERNAME], $file_id);

        // Create reply records for posts replied to in this new post.
        $matches = [];
        preg_match_all('/(&gt;&gt;)([0-9]+)/', $content, $matches);
        if (!empty($matches) && array_key_exists(2, $matches)) {
            foreach ($matches[2] as $digits) {
                PostModel::addReplyToPost($digits, $post_id);
            }
        }

        header('Location: index.php?board/dir=' . $board_dir . '/thread=' . $thread_id . '#p' . $post_id);
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