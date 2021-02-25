<?php

namespace Dalton\ThreeLeaf\Controllers;

require_once ROOT_PATH.'framework/Controller.php';
require_once ROOT_PATH.'framework/View.php';
require_once ROOT_PATH.'3leaf/Models/UserModel.php';
require_once ROOT_PATH.'3leaf/global_const.php';

use Dalton\Framework\ControllerBase;
use Dalton\Framework\View;
use Dalton\ThreeLeaf\Models\UserModel;

const HASH_ALGO = 'sha3-512';
const WAS_WELCOMED = 'was_welcomed';

class Session extends ControllerBase {

    public $error = '';

    public function showLoginPageTask() {
        View::render('LoginView.php', ['error' => $this->error]);
    }

    public function showRegisterPageTask() {
        View::render('RegisterView.php', ['error' => $this->error]);
    }

    public function processLoginFormTask() {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $password = hash(HASH_ALGO, $password);

        $password_check = UserModel::getPasswordFromUsername($username);

        if ($password_check != '' && $password == $password_check) {
            $this->setupSession($username);
            header('Location: index.php?login/welcome');
        } else {
            http_response_code(409);
            $this->error = 'Invalid username or password.';
            $this->showLoginPageTask();
            die();
        }
    }

    public function processRegisterFormTask() {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        if ($username == '') {
            http_response_code(409);
            $this->error = "Username cannot be empty.";
            $this->showRegisterPageTask();
            die();
        }

        if (!preg_match('/[a-zA-Z0-9]+/', $username)) {
            http_response_code(409);
            $this->error = "Usernames should only contain alphanumeric characters.";
            $this->showRegisterPageTask();
            die();
        }

        if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $password)) {
            http_response_code(409);
            $this->error = "Password must contain between 8 and 20 characters, include one number, include one upper case character, and include one symbol.";
            $this->showRegisterPageTask();
            die();
        }

        $password = hash(HASH_ALGO, $password);

        $errString = UserModel::tryCreateUser($username, $password);
        if ($errString == '') {
            $this->setupSession($username);
            header('Location: index.php?register/welcome');
        } else {
            http_response_code(409);
            $this->error = $errString;
            $this->showRegisterPageTask();
            die();
        }
    }

    public function logoutTask() {
        $this->destroySession();
        header('Location: index.php');
    }

    public function welcomeLoginTask() {
        if (!isset($_SESSION[WAS_WELCOMED])) {
            $header = 'Welcome ' . $_SESSION[USERNAME] . '!';
            $content = 'Please enjoy your stay.';
            View::render('MessageView.php', ['header' => $header, 'content' => $content]);
            $_SESSION[WAS_WELCOMED] = true;
        } else {
            header('Location: index.php');
        }
    }

    public function welcomeRegisterTask() {
        if (!isset($_SESSION[WAS_WELCOMED])) {
            $header =  'Account created!';
            $content = 'Welcome to 3leaf ' . $_SESSION[USERNAME] . '! You are now logged in.';
            View::render('MessageView.php', ['header' => $header, 'content' => $content]);
            $_SESSION[WAS_WELCOMED] = true;
        } else {
            header('Location: index.php');
        }
    }

    public function setupSession($username) {
        $user = UserModel::fetchUser($username);
        if ($user != null) {
            $_SESSION[USERNAME] = $username;
            $_SESSION[LOGGED_IN] = true;
            $_SESSION[ROLE] = (int) $user['role'];
            $_SESSION[USER_ID] = (int) $user['id'];
        }
    }

    public function destroySession() {
        session_unset();
        session_destroy();
    }

}

?>