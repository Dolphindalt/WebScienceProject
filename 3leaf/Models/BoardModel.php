<?php

namespace Dalton\ThreeLeaf\Models;

require_once ROOT_PATH.'framework/Model.php';

use PDO;
use Dalton\Framework\Model;

class BoardModel extends Model {

    public static function getBoards() {
        $statement = Model::getDB()->prepare('SELECT * FROM selectBoards;');
        $result_set = $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

}

?>