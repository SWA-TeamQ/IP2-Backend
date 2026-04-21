<?php
namespace App\Core;

class Router {
    protected array $routes = [];
    public Request $request;
    public Response $response;

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }
    protected array $middlewares = [];

    public function middleware($path, $middlewareClass) {
        $this->middlewares[$path][] = $middlewareClass;
    }

    public function get($path, $callback) {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['post'][$path] = $callback;
    }
    public function put($path, $callback) {
        $this->routes['PUT'][$path] = $callback;
    }

    public function delete($path, $callback) {
        $this->routes['DELETE'][$path] = $callback;
    }

    public function resolve() {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if (isset($this->middlewares[$path])) {
                foreach ($this->middlewares[$path] as $middleware) {
                    $m = new $middleware();
                    $result = $m->handle($this->request, $this->response);
                    if ($result !== true) return; // Middleware blocked the request
                }
            }
        if ($callback === false) {
            $this->response->setStatusCode(404);
            return $this->response->json(['error' => 'Route not found']);
        }

        if (is_array($callback)) {
            $controller = new $callback[0]();
            $methodName = $callback[1];
            return $controller->$methodName($this->request, $this->response);
        }

        return call_user_func($callback, $this->request, $this->response);
    }
}