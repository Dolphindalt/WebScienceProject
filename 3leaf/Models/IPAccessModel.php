<?php

namespace Dalton\ThreeLeaf\Models;

require_once ROOT_PATH.'framework/Model.php';

use PDO;
use Dalton\Framework\Model;

class IPAccessModel extends Model {

    public static function insertIPAccess($ip_addr, $operation) {
        $statement = Model::getDB()->prepare("CALL insertIpAccess(?, ?)");
        $statement->bindParam(1, $ip_addr, PDO::PARAM_STR, 36);
        $statement->bindParam(2, $operation, PDO::PARAM_STR, 64);
        $statement->execute();
    }

    public static function testIPAccess($ip_addr, $operation, $access_timer_seconds) {
        $statement = Model::getDB()->prepare("CALL testIpAccess(?, ?, ?, @can_access_bool)");
        $statement->bindParam(1, $ip_addr, PDO::PARAM_STR, 36);
        $statement->bindParam(2, $operation, PDO::PARAM_STR, 64);
        $statement->bindParam(3, $access_timer_seconds, PDO::PARAM_INT);
        $statement->execute();
        $statement = Model::getDB()->prepare("SELECT @can_access_bool");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result)) {
            return false;
        } else {
            return (bool)$result[0]['@can_access_bool'];
        }
    }

}

?>