<?php

namespace Dalton\ThreeLeaf\Models;

require_once ROOT_PATH.'framework/Model.php';

use PDO;
use Dalton\Framework\Model;

class Board {
    private $name;
    private $directory;

    public function __construct($name, $directory) {
        $this->name = $name;
        $this->directory = $directory;
    }

    public function getName() {
        return $this->name;
    }

    public function getDirectory() {
        return $this->directory;
    }
}

class BoardModel extends Model {

    public static function getBoards() {
        $statement = Model::getDB()->prepare('SELECT * FROM selectBoards;');
        $result_set = $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

}

?>