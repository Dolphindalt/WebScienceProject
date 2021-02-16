<?php

namespace Dalton\Framework;

abstract class ControllerBase {

    protected $params = [];

    public function __construct($params) {
        $url = strtok($_SERVER['QUERY_STRING'], '?');
        $pot_params = preg_split("/\//", $url);
        foreach ($pot_params as $value) {
            if (!empty(preg_match('/[a-zA-z]+[^=]=[^\}]+/', $value))) {
                $kv_split = preg_split("/=/", $value);
                $params[$kv_split[0]] = $kv_split[1];
            }
        }
        $this->params = $params;
    }

    // Called when a method that does not exist is called.
    // Used to allow indirect calls to methods with Task suffix.
    public function __call($name, $args) {
        $method = $name . 'Task';
        if (method_exists($this, $method)) {
            if ($this->init() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->cleanup();
            }
        }
        else {
            throw new \Exception("Failed to find task " . $method . " in " . get_class($this));
        }
    }

    protected function init() {

    }

    protected function cleanup() {

    }

    public function pageNotFound() {
        http_response_code(404);
        include(ROOT_PATH.'3leaf/Views/NotFoundView.php');
        die();
    }

    public function strip_html_and_slashes_and_non_spaces($input) {
        $input = stripslashes($input);
        $input = htmlspecialchars($input);
        $input = trim($input, ['\n', '\t', '\0', '\x0B', '\r']);
        $input = rtrim($input);
        $input = ltrim($input);
        return $input;
    }
}

?>