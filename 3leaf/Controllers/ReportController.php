<?php

namespace Dalton\ThreeLeaf\Controllers;

require_once ROOT_PATH.'framework/Controller.php';
require_once ROOT_PATH.'framework/View.php';
require_once ROOT_PATH.'3leaf/Models/ReportModel.php';
require_once ROOT_PATH.'3leaf/Services/PostTimerService.php';

use Dalton\Framework\View;
use Dalton\Framework\ControllerBase;
use Dalton\ThreeLeaf\Models\ReportModel;
use Dalton\ThreeLeaf\Services\PostTimerService;

class Reports extends ControllerBase {

    public function showReportsTask() {
        if (!isset($_SESSION[LOGGED_IN]) || $_SESSION[ROLE] != 1) {
            http_response_code(401);
            die();
        }
        $reports = ReportModel::getReports();
        View::render('ReportView.php', ['reports' => $reports]);
    }

    public function deleteReportTask() {
        if (!isset($_SESSION[LOGGED_IN]) || $_SESSION[ROLE] != 1) {
            http_response_code(401);
            die();
        }

        if (!array_key_exists('report_id', $this->params)) {
            $this->pageNotFound();
        }

        $report_id = $this->params['report_id'];

        ReportModel::deleteReport($report_id);
    }

    public function createReportTask() {
        if (!isset($_SESSION[LOGGED_IN])) {
            http_response_code(401);
            die();
        }

        if (!array_key_exists('post_id', $this->params)) {
            $this->pageNotFound();
        }

        // Prevent spamming via timer.
        if (!PostTimerService::testPostTimer(OP_CREATE_REPORT, 60)) {
            http_response_code(429);
            die();
        }

        $post_id = $this->params['post_id'];

        if (ReportModel::createReport($post_id)) {
            PostTimerService::insertIPRecord(OP_CREATE_REPORT);
            http_response_code(200);
        } else {
            http_response_code(409);
        }
    }

}

?>