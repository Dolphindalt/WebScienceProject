<?php

namespace Dalton\ThreeLeaf\Controllers;

require_once ROOT_PATH.'framework/Controller.php';
require_once ROOT_PATH.'3leaf/Models/ThreadModel.php';
require_once ROOT_PATH.'3leaf/Controllers/FileController.php';
require_once ROOT_PATH.'3leaf/Models/BoardModel.php';

use Dalton\ThreeLeaf\Models\ThreadModel;
use Dalton\ThreeLeaf\Controllers\FileUpload;
use Dalton\Framework\ControllerBase;
use Dalton\ThreeLeaf\Models\BoardModel;

class Threads extends ControllerBase {

    public function listFromBoardTask() {
        $boardThreads = ThreadModel::getThreads($this->params['board']);
        echo json_encode($boardThreads);
    }

    public function createThreadTask() {
        $board_dir = $this->params['dir'];
        if (!BoardModel::isBoardDirectoryValid($board_dir)) {
            http_response_code(400);
            echo 'Invalid board directory';
            die();
        }

        echo print_r($_POST);
        $thread_name = $_POST['name'];
        $content = $_POST['comment'];

        if (empty($thread_name)) {
            http_response_code(400);
            echo 'Thread name required.';
            die();
        }

        if (empty($content)) {
            http_response_code(400);
            echo 'Thread comment required.';
            die();
        }

        $file_controller = new FileUpload();
        $file_id = $file_controller->tryUploadFile();
        if ($file_id == null) {
            echo 'Failed to upload file.';
            return;
        }

        $content = nl2br($content);
        $thread_name = $this->strip_html_and_slashes_and_non_spaces($thread_name);
        $content = $this->strip_html_and_slashes_and_non_spaces($content);
        ThreadModel::createThread($board_dir, $thread_name, $content, $_SESSION[USERNAME], $file_id);
    }

}

?>