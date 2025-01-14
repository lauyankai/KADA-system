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

            // Remove the base URL part of the URI
            $requestUri = str_replace('/KADA-system', '', $requestUri);

            foreach ($this->routes as $route) {
                // Convert route pattern to regex
                $pattern = $this->convertRouteToRegex($route['route']);

                if ($route['method'] == $requestMethod && preg_match($pattern, $requestUri, $matches)) {
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
            // Log the error
            error_log($e->getMessage());
            // Display user-friendly error message
            echo $e->getMessage();
            return;
        }
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
        array_shift($matches);
        return $matches;
    }

    public function __construct() {
        $this->addRoute('GET', '/users/delete/{id}', 'UserController', 'delete');
        $this->addRoute('GET', '/users', 'UserController', 'index');
        $this->addRoute('GET', '/users/savings/page', 'UserController', 'savingsDashboard');
        $this->addRoute('GET', '/users/savings/deposit', 'UserController', 'showDepositForm');
        $this->addRoute('GET', '/users/savings/transactions', 'UserController', 'showTransactions');
        $this->addRoute('GET', '/users/dashboard', 'UserController', 'dashboard');
        $this->addRoute('GET', '/users/savings/page', 'UserController', 'savingsDashboard');
        $this->addRoute('GET', '/users/loans', 'UserController', 'loans');
        $this->addRoute('GET', '/users/payments', 'UserController', 'payments');
    }
}