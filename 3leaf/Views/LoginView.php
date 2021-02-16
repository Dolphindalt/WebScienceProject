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
        <h2>Login</h2>
    </div>
    <div class='container-content'>
        <form id='loginForm' method='post' name='loginForm' enctype='multipart/form-data' class='form-style'>
            <span><input class='form-input' type='text' name='username' id='username' placeholder="Username" <?php if (isset($_POST['username'])) { echo "value='" . $_POST['username'] . "'"; }?> /></span>
            <span><input class='form-input' type='password' name='password' id='password' placeholder="Password"/></span>
            <button id='loginSubmit' class='center'>Login</button>
            <?php
            if (isset($error)) {
                echo "<p class='error-text center'>$error</p>";
            }
            ?>
        </form>
    </div>
</div>
<script>
    $(document).ready(() => {
        $("#threadSubmit").click(() => {
            $("#username-error-text").css('display', 'none');
            $("#password-error-text").css('display', 'none');
            let local_username = $('#username').prop('files')[0];
            let local_password = $("#password").val();
            $.post("index.php/form?something", {
                username: local_username,
                password: local_password,
            }, (data) => {
                $("#loginForm")[0].reset();
            }, (error) => {
            });
        });
    });
</script>