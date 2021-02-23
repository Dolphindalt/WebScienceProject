<?php

namespace Dalton\ThreeLeaf\Controllers;

require_once ROOT_PATH.'framework/Controller.php';
require_once ROOT_PATH.'framework/View.php';
require_once ROOT_PATH.'3leaf/Models/UserModel.php';

use Dalton\Framework\ControllerBase;
use Dalton\Framework\View;
use Dalton\ThreeLeaf\Models\UserModel;

class UserPage extends ControllerBase {
    
    public function showUserPageTask() {
        // Ensure the username parameter is set.
        if (!isset($this->params['username'])) {
            $this->pageNotFound();
        }

        $username = $this->params['username'];

        $results = UserModel::fetchPostsUserIsActiveIn($username);

        if ($results == null) {
            $this->pageNotFound();
        }

        View::render('UserPageView.php', ['posts' => $results, 'username' => $username]);
    }

}

?>