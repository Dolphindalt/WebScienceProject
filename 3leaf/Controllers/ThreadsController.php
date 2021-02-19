<?php

namespace Dalton\ThreeLeaf\Controllers;

require_once ROOT_PATH.'framework/Controller.php';
require_once ROOT_PATH.'3leaf/Models/ThreadModel.php';
require_once ROOT_PATH.'3leaf/Controllers/FileController.php';
require_once ROOT_PATH.'3leaf/Controllers/ThreadPageController.php';
require_once ROOT_PATH.'3leaf/Controllers/BoardPageController.php';
require_once ROOT_PATH.'3leaf/Models/BoardModel.php';

use Dalton\ThreeLeaf\Models\ThreadModel;
use Dalton\ThreeLeaf\Controllers\FileUpload;
use Dalton\Framework\ControllerBase;
use Dalton\ThreeLeaf\Models\BoardModel;
use Dalton\ThreeLeaf\Controllers\ThreadPage;
use Dalton\ThreeLeaf\Controllers\BoardPage;

class Threads extends ControllerBase {

    public function listFromBoardTask() {
        $boardThreads = ThreadModel::getThreads($this->params['board']);
        echo json_encode($boardThreads);
    }

    public function createThreadTask() {
        $board_dir = $this->params['dir'];

        if (!isset($_SESSION[LOGGED_IN])) {
            $this->showErrorOnBoardCatalog($board_dir, 'You need to log in to make a post.');
        }

        if (!BoardModel::isBoardDirectoryValid($board_dir)) {
            $this->showErrorOnBoardCatalog($board_dir, 'Invalid board directory.');
        }

        $thread_name = $_POST['name'];
        $content = $_POST['comment'];

        if (empty($thread_name)) {
            $this->showErrorOnBoardCatalog($board_dir, 'Thread name required.');
        }

        if (empty($content)) {
            $this->showErrorOnBoardCatalog($board_dir, $board_dir, 'Thread comment required.');
        }

        $file_controller = new FileUpload();
        $results = $file_controller->tryUploadFile($_SESSION[USERNAME]);
        if (array_key_exists('error', $results)) {
            $this->showErrorOnBoardCatalog($board_dir, 'Failed to upload file.');
        }

        $file_id = $results['id'];
        $content = nl2br($content);
        $thread_name = $this->strip_html_and_slashes_and_non_spaces($thread_name);
        $content = $this->strip_html_and_slashes_and_non_spaces($content);
        $thread = ThreadModel::createThread($board_dir, $thread_name, $content, $_SESSION[USERNAME], $file_id);

        $thread_page_controller = new ThreadPage([]);
        $thread_page_controller->showThreadPageManual($board_dir, $thread['id'], null);
    }

    private function showErrorOnBoardCatalog($dir, $error) {
        $board_page_controller = new BoardPage([]);
        http_response_code(400);
        $board_page_controller->showBoardCatalogWithError($dir, $error);
        die();
    }

}

?>