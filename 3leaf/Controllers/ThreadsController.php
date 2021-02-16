<?php

namespace Dalton\ThreeLeaf\Controllers;

require_once ROOT_PATH.'framework/Controller.php';
require_once ROOT_PATH.'3leaf/Models/ThreadModel.php';

use Dalton\ThreeLeaf\Models\ThreadModel;
use Dalton\Framework\ControllerBase;

class Threads extends ControllerBase {

    public function listFromBoardTask() {
        $boardThreads = ThreadModel::getThreads($this->params['board']);
        echo json_encode($boardThreads);
    }

    public function createThreadFromFormTask() {
        $image_data = $_POST['image'];
        $thread_name = $_POST['name'];
        $content = $_POST['comment'];

        if (empty($image_data)) {
            http_response_code(400);
            echo 'Image data required.';
            die();
        }

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

        $content = nl2br($content);
        $thread_name = $this->strip_html_and_slashes_and_non_spaces($thread_name);
        $content = $this->strip_html_and_slashes_and_non_spaces($content);
    }

}

?>