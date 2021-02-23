<?php

namespace Dalton\ThreeLeaf\Controllers;

require_once ROOT_PATH.'framework/Controller.php';
require_once ROOT_PATH.'framework/View.php';

use Dalton\Framework\View;
use Dalton\Framework\ControllerBase;

class Home extends ControllerBase {

    public function showTask() {
        View::render('HomeView.php');
    }

    public function showRulesTask() {
        View::render('RulesView.php');
    }

    public function showFAQTask() {
        View::render('FAQView.php');
    }
}

?>