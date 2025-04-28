<?php

namespace App\Router;

class UrlGenerator
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function generate(string $name, array $parameters = []): string
    {
        return $this->router->url($name, $parameters);
    }
}
