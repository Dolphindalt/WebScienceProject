<?php

namespace Dalton\ThreeLeaf\Services;

require_once ROOT_PATH.'3leaf/Models/IPAccessModel.php';

use Dalton\ThreeLeaf\Models\IPAccessModel;

class PostTimerService {

    public static function getUserIP() {
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function insertIPRecord($operation) {
        $ip = PostTimerService::getUserIP();
        if (!isset($ip)) {
            return;
        }

        IPAccessModel::insertIPAccess($ip, $operation);
    }

    public static function testPostTimer($operation, $time_seconds) {
        $ip = PostTimerService::getUserIP();
        if (!isset($ip)) {
            return true;
        }

        $has_access = IPAccessModel::testIPAccess($ip, $operation, $time_seconds);
        return $has_access;
    }

}

?>