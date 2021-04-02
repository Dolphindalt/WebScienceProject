<?php

namespace Dalton\ThreeLeaf\Services;

require_once ROOT_PATH.'3leaf/Models/IPAccessModel.php';

use Dalton\ThreeLeaf\Models\IPAccessModel;

class PostTimerService {

    public static function insertIPRecord($operation) {
        $ip = getUserIP();
        if (!isset($ip)) {
            return;
        }

        IPAccessModel::insertIPAccess($ip, $operation);
    }

    public static function testPostTimer($operation, $time_seconds) {
        $ip = getUserIP();
        if (!isset($ip)) {
            return true;
        }

        $has_access = IPAccessModel::testIPAccess($ip, $operation, $time_seconds);
        return $has_access;
    }

}

?>