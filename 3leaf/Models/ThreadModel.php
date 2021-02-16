<?php

namespace Dalton\ThreeLeaf\Models;

require_once ROOT_PATH.'framework/Model.php';

use PDO;
use Dalton\Framework\Model;

class ThreadModel extends Model {

    public static function getThreads($board_dir) {
        $statement = Model::getDB()->prepare("CALL selectThreadsFromBoard(?)");
        $statement->bindParam(1, $board_dir, PDO::PARAM_STR);
        $result_set = $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    public static function getThread($thread_id) {
        $statement = Model::getDB()->prepare("CALL selectThreadById(?)");
        $statement->bindParam(1, $thread_id, PDO::PARAM_STR);
        $result_set = $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results[0];
    }

}

?>