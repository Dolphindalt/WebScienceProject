<?php

namespace Dalton\Framework;

abstract class ControllerBase {

    protected $params = [];

    public function __construct($params) {
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
}

?>