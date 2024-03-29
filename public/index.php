<?php

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

require_once ROOT_PATH.'3leaf/global_const.php';

if (!isset($_SESSION)) {
	/*session_set_cookie_params([
		'path' => dirname(__DIR__) . '/public',
		'domain' => $_SERVER['HTTP_HOST'],
		'secure' => true
	]);*/
    session_start();
}

if (array_key_exists(IP_ADDR, $_SESSION) && $_SESSION[IP_ADDR] != getUserIP()) {
    destroy_session();
    session_start();
}

use Dalton\ThreeLeaf\Controllers\Sidenav;

require_once ROOT_PATH.'config.php';
require_once ROOT_PATH.'3leaf/Models/BoardModel.php';

use Dalton\ThreeLeaf\Models\BoardModel;

?>

<!DOCTYPE html>
<html>
<head>
    <title>3leaf</title>
    <link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
    <link rel='stylesheet' type='text/css' href='css/style.css' />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 
    <script type='text/javascript' src='js/global.js'/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 
</head>
<body>
    <?php 
        include_once(ROOT_PATH.'3leaf/Controllers/SidenavController.php');
        $sc = new Sidenav($params = []);
        $sc->showTask();
    ?>
    <div class='home-grid-container'>
        <div class='main-block'>
            <div class='link-header-wrapper'>
                <span style='float:left;'>
                    <?php
                        echo "[ ";
                        $boards = BoardModel::getBoards();
                        foreach ($boards as $kv) {
                            $dir = $kv['directory'];
                            $name = $kv['name'];
                            echo "<a href='index.php?board/dir=" . $dir . "'>" . $dir . "</a> | ";
                        }
                        echo "<a href='index.php'>Home</a>";
                        echo " ]";
                    ?>
                </span>
                <span style='float:right;'>
                    <?php
                        if (!isset($_SESSION[LOGGED_IN])) {
                            echo "[ <a href='index.php?login'>Login</a> | <a href='index.php?register'>Register</a> ]";
                        } else {
                            echo "[ <a href='index.php?logout'>Logout</a> ]";
                        }
                    ?>
                </span>
            </div>
            <div class='main-wrapper'>
                <div class='main-div'>
                    <div class='logo-div'>
                        <a href='index.php' title='Home' style='display:block; width: 400px; margin: 0 auto;'>
                            <img src='assets/logo.png' alt='3leaf'/>
                        </a>
                    </div>
                    <div>
                        <?php include_once(ROOT_PATH.'public/router.php'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="snackbar"></div>
</body>
</html>
