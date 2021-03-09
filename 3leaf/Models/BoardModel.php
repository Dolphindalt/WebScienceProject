<?php

namespace Dalton\ThreeLeaf\Models;

require_once ROOT_PATH.'framework/Model.php';

use PDO;
use Dalton\Framework\Model;

class BoardModel extends Model {

    public static function getBoards() {
        $statement = Model::getDB()->prepare('SELECT * FROM selectBoards;');
        $result_set = $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    public static function isBoardDirectoryValid($dir) {
        $boards = BoardModel::getBoards();
        foreach ($boards as $kv) {
            if ($kv['directory'] == $dir) {
                return true;
            }
        }
        return false;
    }

    public static function getBoardIdFromDirectory($dir) {
        $statement = Model::getDB()->prepare('CALL getBoardIdFromDirectory(?, @board)');
        $statement->bindParam(1, $dir, PDO::PARAM_STR);
        $result_set = $statement->execute();
        $statement->closeCursor();
        $result = Model::getDB()->query('SELECT @board AS board')->fetch(PDO::FETCH_ASSOC);
        return $result['board'];
    }

    public static function getBoardFromDirectory($dir) {
        $statement = Model::getDB()->prepare('CALL getBoardFromDirectory(?)');
        $statement->bindParam(1, $dir, PDO::PARAM_STR);
        $result_set = $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return !empty($result) ? $result[0] : null;
    }

}

?>