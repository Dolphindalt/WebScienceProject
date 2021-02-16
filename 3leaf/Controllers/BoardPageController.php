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

class BoardPage extends ControllerBase {

    public function showBoardCatalogTask() {
        // Ensure the directory parameter is set.
        if (!isset($this->params['dir'])) {
            $this->pageNotFound();
        }
        // Ensure the board exists.
        $board_directory = strtolower($this->params['dir']);
        $board = BoardModel::getBoardFromDirectory($board_directory);
        if (empty($board)) {
            $this->pageNotFound();
        }
        // We need to fetch a list of all threads.
        $threads = ThreadModel::getThreads($board_directory);
        $rootPosts = [];
        foreach ($threads as $thread) {
            $root = PostModel::fetchRootPostFromThread($thread['id']);
            array_push($rootPosts, $root);
        }
        View::render('BoardCatalogView.php', $args = ['board' => $board, 'threads' => $threads, 'root_posts' => $rootPosts]);
    }

}

?>