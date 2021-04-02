<?php

// User session constants.
const USERNAME = 'username';
const LOGGED_IN = 'logged_in';
const ROLE = 'user_role';
const USER_ID = 'user_id';
const IP_ADDR = 'ip_address';

// Options constants.
const DARK_MODE = 'dark_mode';

// Operation names.
const OP_REGISTER = 'register';
const OP_CREATE_POST_OR_THREAD = 'post_or_thread';
const OP_CREATE_REPORT = 'report';

// User roles.
const MODERATOR = 1;
const DEFAULT_USER = 0;

function getUserIP() {
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

function destroy_session() {
    session_unset();
    if (ini_get('session.use_cookies')) {
        setcookie(session_name(), '', time() - 42000);
    }
    session_destroy();
}

?>