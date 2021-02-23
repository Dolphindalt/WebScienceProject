<?php

namespace Dalton\ThreeLeaf\Controllers;

require_once ROOT_PATH.'framework/Controller.php';
require_once ROOT_PATH.'3leaf/Models/ThreadModel.php';
require_once ROOT_PATH.'3leaf/Models/PostModel.php';
require_once ROOT_PATH.'3leaf/Models/BoardModel.php';
require_once ROOT_PATH.'framework/View.php';

use Dalton\ThreeLeaf\Models\ThreadModel;
use Dalton\Framework\ControllerBase;
use Dalton\ThreeLeaf\Models\PostModel;
use Dalton\ThreeLeaf\Models\BoardModel;
use Dalton\Framework\View;

class ThreadPage extends ControllerBase {

    public function showThreadPageTask() {
        // Ensure the directory parameter is set.
        if (!isset($this->params['dir']) || !isset($this->params['thread'])) {
            $this->pageNotFound();
        }

        $board_directory = strtolower($this->params['dir']);
        $board = BoardModel::getBoardFromDirectory($board_directory);

        $thread_id = $this->params['thread'];
        $thread = ThreadModel::getThread($thread_id);
        // Ensure the board and thread exist.
        if (empty($board) || empty($thread)) {
            $this->pageNotFound();
        }

        $posts = PostModel::fetchAllPostsFromThread($thread_id);
        
        $this->showThreadPage($thread_id, $board, $thread, $posts);
    }

    private function showThreadPage($thread_id, $board, $thread, $posts) {
        $args = ['board' => $board, 'thread' => $thread, 'posts' => $posts];
        if (array_key_exists('error', $this->params)) {
            $args['error'] = $this->params['error'];
        }
        View::render('ThreadPageView.php', $args);
    }

    public function showThreadPageWithError($dir, $thread_id, $error) {
        $this->params['dir'] = $dir;
        $this->params['thread'] = $thread_id;
        $this->params['error'] = $error;
        $this->showThreadPageTask();
    }

}

?>