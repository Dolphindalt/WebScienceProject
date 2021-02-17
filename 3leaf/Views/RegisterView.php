<?php

require_once ROOT_PATH.'framework/View.php';

use Dalton\Framework\View;

if (isset($_SESSION[LOGGED_IN])) {
    View::render('HomeView.php');
    die();
}

if (isset($args['error'])) {
    $error = $args['error'];
}

?>
<div class='container-wrapper'>
    <div class='container-header'>
        <h2>Register</h2>
    </div>
    <div class='container-content'>
        <form id='registerForm' method='post' name='registerForm' enctype='multipart/form-data' class='form-style'>
            <span><input class='form-input' type='text' name='username' id='username' placeholder='Username' <?php if (isset($_POST['username'])) { echo "value='" . $_POST['username'] . "'"; }?> /></span>
            <span><input class='form-input' type='password' name='password' id='password' placeholder='Password'/></span>
            <button id='loginSubmit' class='center'>Register</button>
            <?php
            if (isset($error)) {
                echo "<p class='error-text center'>$error</p>";
            }
            ?>
        </form>
    </div>
</div>