<?php

namespace Dalton\ThreeLeaf\Models;

require_once ROOT_PATH.'framework/Model.php';

use PDO;
use Dalton\Framework\Model;

class PostModel extends Model {

    public static function selectPostIDsFromPostID($post_id) {
        $statement = Model::getDB()->prepare("CALL selectPostIDsByPostID(?)");
        $statement->bindParam(1, $post_id, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($results)) {
            return null;
        } else {
            return $results[0];
        }
    }

    public static function fetchRootPostFromThread($thread_id) {
        $statement = Model::getDB()->prepare("CALL selectRootPostFromThread(?)");
        $statement->bindParam(1, $thread_id, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results[0];
    }

    public static function fetchAllPostsFromThread($thread_id) {
        $statement = Model::getDB()->prepare("CALL selectPostsFromThread(?)");
        $statement->bindParam(1, $thread_id, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    public static function createPostInThread($board_dir, $thread_id, $content, $uploader_name, $file_id) {
        $statement = Model::getDB()->prepare("CALL createPost(?, ?, ?, ?, ?)");
        $statement->bindParam(1, $board_dir, PDO::PARAM_STR, 12);
        $statement->bindParam(2, $thread_id, PDO::PARAM_INT);
        $statement->bindParam(3, $content, PDO::PARAM_STR, 8192);
        $statement->bindParam(4, $uploader_name, PDO::PARAM_STR, 36);
        $statement->bindParam(5, $file_id, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results[0]['LAST_INSERT_ID()'];
    }

}

?>