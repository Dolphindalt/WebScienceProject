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

    public function encodeClassObjectsToJSON($array) {
        $exposed_array = [];
        foreach ($array as $value) {
            array_push($exposed_array, $value->expose());
        }
        return $exposed_array;
    }
}

?>