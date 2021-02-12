<?php

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>3leaf</title>
    <link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
    <link rel='stylesheet' type='text/css' href='css/style.css' />
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
</head>
<body>
    <div class='main-div'>
        <div class='logo-div'>
            <a href='index.php' title='Home'>
                <img src='assets/logo.png' alt='3leaf' />
            </a>
        </div>
        <div>
            <?php include_once(ROOT_PATH.'public/router.php'); ?>
        </div>
    </div>
</body>
</html>