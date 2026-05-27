<?php

namespace App\Core;

class Router
{
    protected array $routes = [];
    protected array $middlewares = [];
    protected string $prefix = '';
    protected $errorHandler = null;
    public Request $request;
    public Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function setErrorHandler(callable $handler)
    {
        $this->errorHandler = $handler;
    }

    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }

    public function group(string $prefix, callable $callback)
    {
        $oldPrefix = $this->prefix;
        $this->prefix .= $prefix;
        $callback($this);
        $this->prefix = $oldPrefix;
    }

    public function middleware($path, $middlewareClass)
    {
        $this->middlewares[$this->prefix . $path][] = $middlewareClass;
    }

    public function get($path, $callback)
    {
        echo "lksdjfldskfj";
        $this->routes['get'][$this->prefix . $path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['post'][$this->prefix . $path] = $callback;
    }

    public function patch($path, $callback)
    {
        $this->routes['patch'][$this->prefix . $path] = $callback;
    }

    public function delete($path, $callback)
    {
        $this->routes['delete'][$this->prefix . $path] = $callback;
    }

    public function resolve()
    {
        try {
            $path = $this->request->getPath();
            $method = strtolower($this->request->getMethod());

            $callback = false;
            $params = [];

            // 1. Check for exact match first
            if (isset($this->routes[$method][$path])) {
                $callback = $this->routes[$method][$path];
            } elseif (isset($this->routes[$method])) {
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
                        if ($result !== true) {
                            return;
                        }
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
        } catch (\Throwable $e) {
            if ($this->errorHandler) {
                return call_user_func($this->errorHandler, $e, $this->request, $this->response);
            }
            // Default error response if no handler set
            return $this->response->error('Internal Server Error: ' . $e->getMessage(), 500);
        }
    }
}

