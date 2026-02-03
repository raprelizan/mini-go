<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][] = [$path, $handler];
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][] = [$path, $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';

        foreach ($this->routes[$method] ?? [] as [$route, $handler]) {
            $pattern = $this->toPattern($route);
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                call_user_func_array($handler, $matches);
                return;
            }
        }

        http_response_code(404);
        echo 'Page not found.';
    }

    private function toPattern(string $route): string
    {
        $route = rtrim($route, '/') ?: '/';
        $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $route);
        return '#^' . $pattern . '$#';
    }
}
