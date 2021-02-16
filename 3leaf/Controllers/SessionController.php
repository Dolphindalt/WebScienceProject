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

        if ($password_check && $password == $password_check) {
            $this->setupSession($username);
            echo 'Welcome ' . $_SESSION[USERNAME] . '!';
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

        if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $password)) {
            http_response_code(409);
            $this->error = "Password must contain between 8 and 20 characters, include one number, include one upper case character, and include one symbol.";
            $this->showRegisterPageTask();
            die();
        }

        $password = hash(HASH_ALGO, $password);

        $errString = UserModel::tryCreateUser($username, $password);
        if ($errString == '') {
            echo 'Account created!';
        } else {
            http_response_code(409);
            $this->error = $errString;
            $this->showRegisterPageTask();
            die();
        }
    }

    public function logoutTask() {
        $this->destroySession();
    }

    public function setupSession($username) {
        $_SESSION[USERNAME] = $username;
        $_SESSION[LOGGED_IN] = true;
    }

    public function destroySession() {
        session_unset();
        session_destroy();
    }

}

?>