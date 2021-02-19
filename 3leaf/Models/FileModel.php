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

}

?>