<?php

namespace Dalton\ThreeLeaf\Models;

require_once ROOT_PATH.'framework/Model.php';

use PDO;
use Dalton\Framework\Model;

class FileModel extends Model {

    public static function insertFileRecord($file_name, $uploader_name) {
        $statement = Model::getDB()->prepare("CALL insertFileRecord(?, ?)");
        $statement->bindParam(1, $file_name, PDO::PARAM_STR, 40);
        $statement->bindParam(2, $uploader_name, PDO::PARAM_STR, 36);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($results)) {
            return '';
        } else {
            return $results[0];
        }
    }

    public static function getFileRecordFromPostID($post_id) {
        $statement = Model::getDB()->prepare("CALL getFileRecordFromPostID(?)");
        $statement->bindParam(1, $post_id, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($results)) {
            return null;
        } else {
            return $results[0];
        }
    }

    public static function getFileRecordsFromThreadID($thread_id) {
        $statement = Model::getDB()->prepare("CALL fetchFileRecordsFromThread(?)");
        $statement->bindParam(1, $thread_id, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($results)) {
            return null;
        } else {
            return $results;
        }
    }

    public static function deleteFileRecord($file_id) {
        $statement = Model::getDB()->prepare("CALL deleteFileRecord(?)");
        $statement->bindParam(1, $file_id, PDO::PARAM_INT);
        $statement->execute();
    }

}

?>