<?php

namespace Dalton\Framework;

require_once ROOT_PATH.'config.php';

use PDO;

abstract class Model {

    private static $db = null;

    protected static function getDB() {
        if (Model::$db === null) {
            $dbs = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
            Model::$db = new PDO($dbs, DB_USER, DB_PASS);

            Model::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return Model::$db;
    }

}

?>