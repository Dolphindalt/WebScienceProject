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

    public static function selectPostByID($post_id) {
        $statement = Model::getDB()->prepare("CALL selectPostByID(?)");
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

    public static function getRepliesToPost($post_id) {
        $statement = Model::getDB()->prepare("CALL getRepliesToPost(?)");
        $statement->bindParam(1, $post_id, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    public static function addReplyToPost($parent_post_id, $reply_post_id) {
        $statement = Model::getDB()->prepare("CALL createPostReplyRecord(?, ?)");
        $statement->bindParam(1, $parent_post_id, PDO::PARAM_INT);
        $statement->bindParam(2, $reply_post_id, PDO::PARAM_INT);
        $statement->execute();
    }

    public static function deletePost($post_id, $username) {
        $statement = Model::getDB()->prepare("CALL deletePost(?, ?, @had_permission)");
        $statement->bindParam(1, $post_id, PDO::PARAM_INT);
        $statement->bindParam(2, $username, PDO::PARAM_STR, 36);
        $statement->execute();
        $statement = Model::getDB()->query('SELECT @had_permission;');
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result)) {
            return false;
        } else {
            return (bool)$result[0]['@had_permission'];
        }
    }

}

?>