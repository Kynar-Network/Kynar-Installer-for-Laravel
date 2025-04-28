<?php
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/core/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Load helper functions
require_once __DIR__ . '/core/Helpers/general.php';
require_once __DIR__ . '/core/Helpers/lang.php';
require_once __DIR__ . '/core/Helpers/route.php';
require_once __DIR__ . '/core/Helpers/url.php';
