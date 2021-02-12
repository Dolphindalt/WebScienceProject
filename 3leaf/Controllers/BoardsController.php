<?php 

namespace Dalton\ThreeLeaf\Controllers;

require_once ROOT_PATH.'framework/Controller.php';
require_once ROOT_PATH.'3leaf/Models/BoardModel.php';

use Dalton\ThreeLeaf\Models\BoardModel;
use Dalton\Framework\ControllerBase;

class Boards extends ControllerBase {
    public function listTask() {
        $boards = BoardModel::getBoards();
        echo json_encode($boards);
    }
};

?>