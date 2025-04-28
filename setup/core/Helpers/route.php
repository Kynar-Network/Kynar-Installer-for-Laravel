<?php

/**
 * Check if a named route exists
 */
if (!function_exists('route_exists')) {
    function route_exists(string $name): bool
    {
        global $router;
        return $router->hasRoute($name);
    }
}
