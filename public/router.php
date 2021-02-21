<?php

if (!isset($_SESSION)) {
    session_start();
}

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

require_once ROOT_PATH.'config.php';

// Because we cannot use composer class auto loading, 
// we need to require classes to use them.
require_once ROOT_PATH.'3leaf/Controllers/HomeController.php';
require_once ROOT_PATH.'3leaf/Controllers/BoardsController.php';
require_once ROOT_PATH.'3leaf/Controllers/ThreadsController.php';
require_once ROOT_PATH.'3leaf/Controllers/SidenavController.php';
require_once ROOT_PATH.'3leaf/Controllers/BoardPageController.php';
require_once ROOT_PATH.'3leaf/Controllers/ThreadPageController.php';
require_once ROOT_PATH.'3leaf/Controllers/SessionController.php';

require_once ROOT_PATH.'framework/Router.php';
require_once ROOT_PATH.'framework/ExceptionHandler.php';

use Dalton\Framework\Router;

error_reporting(E_ALL);
set_error_handler('Dalton\Framework\ExceptionHandler::errorHandler');
set_exception_handler('Dalton\Framework\ExceptionHandler::exceptionHandler');

$router = new Router();
// POST requests made on a page using PHP form processor MUST use the 
// same URL as the GET method for the page. Do not know how to fix it. 
// Gave up after a few hours. It still works fine despite this defect.

$router->add('', 
    ['controller' => 'Dalton\ThreeLeaf\Controllers\Home', 
    'task' => 'show', 'method' => 'GET']);

$router->add('boards', 
    ['controller' => 'Dalton\ThreeLeaf\Controllers\Boards', 
    'task' => 'list', 'method' => 'GET']);

$router->add('board/{dir=[a-zA-Z]*}', 
    ['controller' => 'Dalton\ThreeLeaf\Controllers\BoardPage',
    'task' => 'showBoardCatalog', 'method' => 'GET']);

$router->add('board/{dir=[a-zA-Z]*}', 
    ['controller' => 'Dalton\ThreeLeaf\Controllers\Threads', 
    'task' => 'createThread', 'method' => 'POST']);

$router->add('board/{dir=[a-zA-Z]*}/{thread=\d*}', 
    ['controller' => 'Dalton\ThreeLeaf\Controllers\ThreadPage',
    'task' => 'showThreadPage', 'method' => 'GET']);

    $router->add('board/{dir=[a-zA-Z]*}/{thread=\d*}', 
    ['controller' => 'Dalton\ThreeLeaf\Controllers\Threads',
    'task' => 'createPostOnThread', 'method' => 'POST']);

$router->add('threads/{board=[a-zA-Z]*}', 
    ['controller' => 'Dalton\ThreeLeaf\Controllers\Threads', 
    'task' => 'listFromBoard', 'method' => 'GET']);

$router->add('login',
    ['controller' => 'Dalton\ThreeLeaf\Controllers\Session', 
    'task' => 'showLoginPage', 'method' => 'GET']);

$router->add('login',
    ['controller' => 'Dalton\ThreeLeaf\Controllers\Session', 
    'task' => 'processLoginForm', 'method' => 'POST']);

$router->add('register',
    ['controller' => 'Dalton\ThreeLeaf\Controllers\Session', 
    'task' => 'showRegisterPage', 'method' => 'GET']);

$router->add('register',
    ['controller' => 'Dalton\ThreeLeaf\Controllers\Session', 
    'task' => 'processRegisterForm', 'method' => 'POST']);

$router->add('logout', 
    ['controller' => 'Dalton\ThreeLeaf\Controllers\Session', 
    'task' => 'logout', 'method' => 'GET']);

$router->add('login/welcome', 
    ['controller' => 'Dalton\ThreeLeaf\Controllers\Session', 
    'task' => 'welcomeLogin', 'method' => 'GET']);

$router->add('register/welcome', 
    ['controller' => 'Dalton\ThreeLeaf\Controllers\Session', 
    'task' => 'welcomeRegister', 'method' => 'GET']);

$router->execute($_SERVER['QUERY_STRING'], $_SERVER['REQUEST_METHOD']); 

?>