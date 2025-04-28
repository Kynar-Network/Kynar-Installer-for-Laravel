<?php
use App\Languages\LanguageManager;

// Instantiate LanguageManager
$languageManager = LanguageManager::getInstance();

// Capture the request method and URI
// Capture the request method and URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

try {
    // Load router configuration
    $router = require __DIR__ . '/configs/routes.php';

    // Handle language redirects ONLY for GET requests
    if ($method === 'GET') {
        $segments = explode('/', trim($uri, '/'));
        if ($segments[0] === 'setup' && (!isset($segments[1]) || $segments[1] === 'step')) {
            $languageManager->redirectToLanguageUrl();
        }
    }

    // Ensure $method is not being overwritten
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        $method = $_SERVER['REQUEST_METHOD']; // Reset to the original method if mismatch detected
    }

    // Process segments (for logging only)
    $segments = explode('/', trim($uri, '/'));

    // Remove 'setup' and language from segments (only for logging)
    if ($segments[0] === 'setup') {
        array_shift($segments);
        if (!empty($segments) && $languageManager->isValidLanguage($segments[0])) {
            array_shift($segments);
        }
    }

    // Dispatch the request with the correct $method and $uri
    $response = $router->dispatch($method, $uri);
    echo $response;
} catch (\Exception $e) {
    header("HTTP/1.0 404 Not Found");
    //echo "Error message: " . $e->getMessage();
    $errorMessage = "Error message: \n" . $e->getMessage();
    require_once __DIR__ . '/404.php';
}

