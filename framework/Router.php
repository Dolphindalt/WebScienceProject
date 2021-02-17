<?php

namespace Dalton\Framework;

use Exception;

class Router {

    private $routes = [];

    public function __construct() {

    }

    public function add($route, $params = []) {
        if (!isset($params['method'])) {
            throw new Exception('Route method must be defined.', 500);
        }
        if (!isset($params['task'])) {
            throw new Exception('Route task must be set.', 500);
        }
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-zA-Z]+)=([^\}]+)\}/', '\1=\2', $route);
        $route = '/^' . $route . '$/i';
        $route_id = $route . " " . $params['method'];
        if (array_key_exists($route_id, $this->routes)) {
            throw new Exception('Duplicate route ID found.', 500);
        }
        $this->routes[$route_id] = $params;
    }

    public function execute($url, $method) {
        $url = strtok($url, '?');
        foreach ($this->routes as $route => $params) {
            $route_path = explode(" ", $route)[0];
            if (preg_match($route_path, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $route_method = $params['method'];
                if (strtoupper($route_method) == $method) {
                    $controller = $params['controller'];
                    if (class_exists($controller)) {
                        $controller = new $controller($params);
                        $task = $params['task'];
                        if (preg_match('/task$/i', $task) == 0) {
                            $controller->$task();
                            return;
                        }
                        throw new \Exception('Failed to call controller task.', 500);
                    }
                    throw new \Exception('Controller undefined for route.', 500);
                }
            }
        }
        throw new \Exception('Route not found.', 404);
    }

}

?>