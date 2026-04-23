<?php
namespace App\Core;

class Router {
    protected array $routes = [];
    protected array $middlewares = [];
    public Request $request;
    public Response $response;

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }

    public function middleware($path, $middlewareClass) {
        $this->middlewares[$path][] = $middlewareClass;
    }

    public function get($path, $callback) {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['post'][$path] = $callback;
    }

    public function patch($path, $callback) {
        $this->routes['patch'][$path] = $callback;
    }

    public function delete($path, $callback) {
        $this->routes['delete'][$path] = $callback;
    }

    public function resolve() {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        
        $callback = false;
        $params = [];

        // 1. Check for exact match first
        if (isset($this->routes[$method][$path])) {
            $callback = $this->routes[$method][$path];
        } else {
            // 2. Check for regex matches
            foreach ($this->routes[$method] as $route => $handler) {
                $routePattern = '#^' . $route . '$#';
                if (preg_match($routePattern, $path, $matches)) {
                    $callback = $handler;
                    array_shift($matches); // Remove the full match
                    $params = $matches;
                    break;
                }
            }
        }

        if ($callback === false) {
            return $this->response->error('Route not found', 404);
        }

        // 3. Handle Middlewares
        foreach ($this->middlewares as $middlewarePath => $classes) {
            $middlewarePattern = '#^' . $middlewarePath . '$#';
            if (preg_match($middlewarePattern, $path)) {
                foreach ($classes as $middlewareClass) {
                    $m = new $middlewareClass();
                    $result = $m->handle($this->request, $this->response);
                    if ($result !== true) return; 
                }
            }
        }

        // 4. Execute Callback
        if (is_array($callback)) {
            $controller = new $callback[0]();
            $methodName = $callback[1];
            return call_user_func_array([$controller, $methodName], array_merge([$this->request, $this->response], $params));
        }

        return call_user_func_array($callback, array_merge([$this->request, $this->response], $params));
    }
}