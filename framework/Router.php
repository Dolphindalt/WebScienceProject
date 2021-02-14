<?php

namespace Dalton\Framework;

class Router {

    private $routes = [];

    public function __construct() {
    }

    public function add($route, $params = []) {
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-zA-Z]+)=([^\}]+)\}/', '\1=\2', $route);
        $route = '/^' . $route . '$/i';
        $this->routes[$route] = $params;
    }

    public function execute($url, $method) {
        $url = strtok($url, '?');

        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
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