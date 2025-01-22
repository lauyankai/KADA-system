<?php

namespace App\Core;

class Router {

    private $routes = [];

    // Add a new route
    public function addRoute($method, $route, $controller, $action) {
        $this->routes[] = [
            'method' => $method,
            'route' => $route,
            'controller' => "App\\Controllers\\$controller",
            'action' => $action
        ];
    }
    
    public function getRegisteredRoutes() {
        return $this->routes;
    }

    // Dispatch the request
    public function dispatch() {
        try {
            $requestUri = $_SERVER['REQUEST_URI'];
            $requestMethod = $_SERVER['REQUEST_METHOD'];

            // Remove query string for matching
            $baseUri = preg_replace('/\?.*$/', '', $requestUri);
            
            // Remove the base URL part of the URI (i.e., /project-root)
            $requestUri = str_replace('/KADA-system', '', $requestUri);

            // Add debug logging
            $this->debugRoute($requestUri, $requestMethod);

            foreach ($this->routes as $route) {
                // Convert route pattern to regex
                $pattern = $this->convertRouteToRegex($route['route']);

                if ($route['method'] == $requestMethod && preg_match($pattern, $baseUri, $matches)) {
                    $controllerName = $route['controller'];
                    $action = $route['action'];

                    // Check if the controller exists
                    if (!class_exists($controllerName)) {
                        throw new \Exception("Controller not found: $controllerName");
                    }

                    $controller = new $controllerName();
                    if (!method_exists($controller, $action)) {
                        throw new \Exception("Action '$action' not found in controller '$controllerName'");
                    }

                    $params = $this->extractRouteParams($route['route'], $requestUri);
                    call_user_func_array([$controller, $action], $params);
                    return;
                }
            }

            throw new \Exception("404 Not Found");

        } catch (\Exception $e) {
            error_log($e->getMessage());
            echo $e->getMessage();
            return;
        }
    }

    // Convert route pattern to regex
    private function convertRouteToRegex($route) {
        // Remove query string for matching
        $baseRoute = preg_replace('/\?.*$/', '', $route);
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $baseRoute);
        return '#^' . $pattern . '$#';
    }

    // Extract parameters from the route
    private function extractRouteParams($routePattern, $requestUri) {
        $pattern = $this->convertRouteToRegex($routePattern);
        preg_match($pattern, $requestUri, $matches);
        array_shift($matches);
        return $matches;
    }

    public function debugRoute($requestUri, $requestMethod) {
        error_log("Requested URI: " . $requestUri);
        error_log("Request Method: " . $requestMethod);
        foreach ($this->routes as $route) {
            error_log("Checking route: " . $route['method'] . " " . $route['route']);
        }
    }
}