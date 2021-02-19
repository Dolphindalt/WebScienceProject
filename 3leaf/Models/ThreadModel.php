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

    public static function createThread($board_dir, 
        $thread_name, $content, $uploader_name, $file_id) {
        $statement = Model::getDB()->prepare("CALL createThread(?, ?, ?, ?, ?)");
        $statement->bindParam(1, $board_dir, PDO::PARAM_STR, 12);
        $statement->bindParam(2, $thread_name, PDO::PARAM_STR, 1024);
        $statement->bindParam(3, $content, PDO::PARAM_STR, 8192);
        $statement->bindParam(4, $uploader_name, PDO::PARAM_STR, 36);
        $statement->bindParam(5, $file_id, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $thread = $results[0];

        

        return $thread;
    }

}

?>