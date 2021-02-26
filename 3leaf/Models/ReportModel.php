<?php

namespace Dalton\ThreeLeaf\Models;

require_once ROOT_PATH.'framework/Model.php';

use PDO;
use Dalton\Framework\Model;

class ReportModel extends Model {
    
    public static function getReports() {
        $statement = Model::getDB()->prepare("SELECT * FROM selectReports;");
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    public static function createReport($post_id) {
        $statement = Model::getDB()->prepare("CALL createReport(?, @did_create)");
        $statement->bindParam(1, $post_id, PDO::PARAM_INT);
        $statement->execute();
        $statement = Model::getDB()->query('SELECT @did_create;');
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result)) {
            return false;
        } else {
            return (bool)$result[0]['@did_create'];
        }
    }

    public static function deleteReport($report_id) {
        $statement = Model::getDB()->prepare("CALL deleteReport(?)");
        $statement->bindParam(1, $report_id, PDO::PARAM_INT);
        $statement->execute();
    }

}

?>