<?php

namespace App\Core;

class Router {

    private $routes = [];

    // Add a new route
    public function addRoute($method, $route, $controller, $action) {
        $this->routes[] = [
            'method' => $method,
            'route' => $route,
            'controller' => "App\\Controller\\$controller", // Fully qualify the controller name
            'action' => $action
        ];
    }
    
    public function getRegisteredRoutes() {
        return $this->routes;
    }

    // Dispatch the request
    public function dispatch() {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Remove the base URL part of the URI (i.e., /project-root)
        $requestUri = str_replace('/yk-mvc', '', $requestUri);

        foreach ($this->routes as $route) {
            // Convert route pattern to regex
            $pattern = $this->convertRouteToRegex($route['route']);

            if ($route['method'] == $requestMethod && preg_match($pattern, $requestUri, $matches)) {
                $controllerName = $route['controller'];
                $action = $route['action'];

                // Check if the controller exists
                if (class_exists($controllerName)) {
                    $controller = new $controllerName();

                    // Extract parameters from the route
                    $params = $this->extractRouteParams($route['route'], $requestUri);

                    // Call the controller method with parameters
                    call_user_func_array([$controller, $action], $params);
                    return;
                } else {
                    echo "Controller not found: $controllerName";
                    return;
                }
            }
        }

        echo "404 Not Found";
    }

    // Convert route pattern to regex
    private function convertRouteToRegex($route) {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route);
        return '#^' . $pattern . '$#';
    }

    // Extract parameters from the route
    private function extractRouteParams($routePattern, $requestUri) {
        $pattern = $this->convertRouteToRegex($routePattern);
        preg_match($pattern, $requestUri, $matches);

        // Remove the full match (index 0)
        array_shift($matches);

        return $matches;
    }
}