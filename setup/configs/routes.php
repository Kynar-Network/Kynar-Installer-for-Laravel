<?php
// configs/routes.php
use App\Router\Router;

$router = new Router();

// Load steps configuration
$stepsConfig = json_decode(file_get_contents(__DIR__ . '/steps.json'), true);

// Base routes
$router->addRoute('GET', '/setup', 'InstallController@index', 'setup.index');
$router->addRoute('GET', '/setup/{lang}', 'InstallController@index', 'setup.index.lang');

// API endpoints
$router->addRoute('POST', '/setup/create-env', 'UtilsController@create_env', 'setup.env.create');
$router->addRoute('POST', '/setup/complete', 'CompletedController@updateIndexFile', 'setup.complete');
$router->addRoute('POST', '/setup/delete-folder', 'UtilsController@deleteFolder', 'setup.delete-folder');
$router->addRoute('GET', '/setup/{lang}/migrate-stream/{key}', 'MigrateStreamController@handle', 'setup.migrate.stream');
$router->addRoute('GET', '/setup/{lang}/dependencies-stream/{key}', 'DependenciesStreamController@handle', 'setup.dependencies.stream');

$router->addRoute('GET', '/setup/{lang}/install-manual-dependencies', 'UtilsController@install_manual', 'setup.install_manual');

// Step routes with language parameter and multiple methods
foreach ($stepsConfig['steps'] as $step) {
    $handler = str_replace('App\\Controllers\\', '', $step['controller']) . '@handle';
    $methods = $step['methods'] ?? ['GET']; // Default to GET if methods not specified

    // Add route for each method
    foreach ($methods as $method) {
        $routeName = "setup.steps.{$step['slug']}"; // Use original slug for route name

        // Add method suffix for non-GET methods
        if (strtoupper($method) !== 'GET') {
            $routeName .= '.' . strtolower($method);
        }

        // Add route with {slug} parameter that will be handled dynamically
        $router->addRoute(
            strtoupper($method),
            "/setup/{lang}/step/{slug}",
            $handler,
            $routeName
        );
    }
}

return $router;
