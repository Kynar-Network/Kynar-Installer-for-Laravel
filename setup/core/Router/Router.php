<?php
namespace App\Router;

class Router
{
    private array $routes = [];
    private array $namedRoutes = [];

    public function addRoute(string $method, string $path, string $handler, ?string $name = null): void
    {
        $route = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'name' => $name,
        ];
        $this->routes[] = $route;
        if ($name) {
            $this->namedRoutes[$name] = $route;
        }
    }

    public function dispatch(string $method, string $uri): mixed
{
    error_log('Dispatching request: ' . $method . ' ' . $uri);
    $uri = rtrim(strtok($uri, '?'), '/');

    foreach ($this->routes as $route) {
        if ($route['method'] !== $method) {
            continue;
        }

        error_log('Checking route: ' . $route['path']);

        // Special handling for step routes
        if (strpos($route['path'], '/setup/{lang}/step/{slug}') === 0) {
            if (preg_match('#^/setup/([^/]+)/step/(.+)$#', $uri, $matches)) {
                $lang = $matches[1];
                $requestedSlug = urldecode($matches[2]);
                error_log('Step route matched. Language: ' . $lang . ', Slug: ' . $requestedSlug);

                // Get step configuration
                $stepsConfig = json_decode(file_get_contents(__DIR__ . '/../../configs/steps.json'), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \RuntimeException("Invalid JSON in steps config: " . json_last_error_msg());
                }

                foreach ($stepsConfig['steps'] as $step) {
                    $translationKey = 'slug.' . $step['slug'];
                    $translatedSlug = t($translationKey) ?: $step['slug_default'];

                    if ($translatedSlug === $requestedSlug) {
                        error_log('Found matching step: ' . json_encode($step));

                        // Create controller instance with proper inheritance
                        $controllerClass = $step['controller'];
                        if (!class_exists($controllerClass)) {
                            throw new \RuntimeException("Controller class not found: {$controllerClass}");
                        }

                        $controller = new $controllerClass();
                        return $controller->handle($lang, $step);
                    }
                }
            }
        }

        $pattern = $this->createPattern($route['path']);
        if (preg_match($pattern, $uri, $matches)) {
            error_log('Route matched: ' . $route['path']);
            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = urldecode($value);
                }
            }
            return $this->handleRoute($route['handler'], $params);
        }
    }

    throw new \RuntimeException("No route found for $method $uri");
}

    private function createPattern(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return "#^" . $pattern . "$#";
    }

    private function handleRoute(string $handler, array $params = []): mixed
    {
        [$controller, $method] = explode('@', $handler);
        $controllerNamespace = 'App\\Controllers\\' . $controller;

        if (!class_exists($controllerNamespace)) {
            throw new \RuntimeException("Controller not found: $controllerNamespace");
        }

        // Special handling for step routes
        if (str_contains($handler, 'Steps\\') && isset($params['slug'])) {
            // Get step configuration
            $stepsConfig = json_decode(file_get_contents(__DIR__ . '/../../configs/steps.json'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException("Invalid JSON in steps config: " . json_last_error_msg());
            }

            // Find the step configuration for this slug
            $step = null;
            foreach ($stepsConfig['steps'] as $stepConfig) {
                if ($stepConfig['slug'] === $params['slug']) {
                    $step = $stepConfig;
                    break;
                }
            }

            if (!$step) {
                throw new \RuntimeException("Step not found: {$params['slug']}");
            }

            // Create controller instance
            $controllerInstance = new $controllerNamespace();
            return $controllerInstance->$method($params['lang'], $step);
        }

        // Regular route handling
        $controllerInstance = new $controllerNamespace();
        return $controllerInstance->$method(...array_values($params));
    }

    public function url(string $name, array $parameters = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \RuntimeException("Route '{$name}' not found");
        }
        $path = $this->namedRoutes[$name]['path'];
        // Replace named parameters
        foreach ($parameters as $key => $value) {
            $path = str_replace("{{$key}}", urlencode($value), $path);
        }
        return $path;
    }
}
