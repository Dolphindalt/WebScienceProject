<?php

namespace Dalton\ThreeLeaf\Controllers;

require_once ROOT_PATH.'framework/Controller.php';
require_once ROOT_PATH.'framework/View.php';
require_once ROOT_PATH.'3leaf/Models/ThreadModel.php';
require_once ROOT_PATH.'3leaf/Models/PostModel.php';

use Dalton\Framework\View;
use Dalton\Framework\ControllerBase;
use Dalton\ThreeLeaf\Models\ThreadModel;
use Dalton\ThreeLeaf\Models\PostModel;

class Home extends ControllerBase {

    public function showTask() {
        $threads = ThreadModel::pullRecentlyUpdatedThreads();
        $rootPosts = [];
        foreach ($threads as $thread) {
            $root = PostModel::fetchRootPostFromThread($thread['id']);
            array_push($rootPosts, $root);
        }

        View::render('HomeView.php', [ 'threads' => $threads, 'root_posts' => $rootPosts ]);
    }

    public function showRulesTask() {
        View::render('RulesView.php');
    }

    public function showFAQTask() {
        View::render('FAQView.php');
    }
}

?>