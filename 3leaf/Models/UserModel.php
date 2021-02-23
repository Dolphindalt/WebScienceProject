<?php

namespace Dalton\ThreeLeaf\Models;

require_once ROOT_PATH.'framework/Model.php';

use PDO;
use Dalton\Framework\Model;

class UserModel extends Model {

    public static function fetchPostsUserIsActiveIn($username) {
        $username = strtolower($username);
        $statement = Model::getDB()->prepare("CALL fetchActivePostsFromUser(?)");
        $statement->bindParam(1, $username, PDO::PARAM_STR, 36);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($results)) {
            return null;
        } else {
            return $results;
        }
    }

    public static function getPasswordFromUsername($username) {
        $username = strtolower($username);
        $statement = Model::getDB()->prepare("CALL findPasswordEntryForUsername(?)");
        $statement->bindParam(1, $username, PDO::PARAM_STR, 36);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($results)) {
            return '';
        } else {
            return $results[0]['password'];
        }
    }

    public static function tryCreateUser($username, $password) {
        $statement = Model::getDB()->prepare("CALL tryCreateUser(?, ?, @err)");
        $statement->bindParam(1, $username, PDO::PARAM_STR, 36);
        $statement->bindParam(2, $password, PDO::PARAM_STR, 255);
        $statement->execute();
        $statement = Model::getDB()->query('SELECT @err;');
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result)) {
            return '';
        } else {
            return $result[0]['@err'];
        }
    }

}

?>