<?php

namespace Dalton\ThreeLeaf\Models;

require_once ROOT_PATH.'framework/Model.php';

use PDO;
use Dalton\Framework\Model;

class PostModel extends Model {

    public static function fetchRootPostFromThread($thread_id) {
        $statement = Model::getDB()->prepare("CALL selectRootPostFromThread(?)");
        $statement->bindParam(1, $thread_id, PDO::PARAM_INT);
        $result_set = $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results[0];
    }

    public static function fetchAllPostsFromThread($thread_id) {
        $statement = Model::getDB()->prepare("CALL selectPostsFromThread(?)");
        $statement->bindParam(1, $thread_id, PDO::PARAM_INT);
        $result_set = $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

}

?>