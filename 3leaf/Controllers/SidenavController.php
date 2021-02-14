<?php

namespace Dalton\ThreeLeaf\Controllers;

require_once ROOT_PATH.'framework/Controller.php';
require_once ROOT_PATH.'framework/View.php';
require_once ROOT_PATH.'3leaf/Models/BoardModel.php';

use Dalton\Framework\ControllerBase;
use Dalton\Framework\View;
use Dalton\ThreeLeaf\Models\BoardModel;

class Sidenav extends ControllerBase {

    public function showTask() {
        $boards = BoardModel::getBoards();
        View::render("SidenavView.php", ['boards' => $boards]);
    }

}

?>