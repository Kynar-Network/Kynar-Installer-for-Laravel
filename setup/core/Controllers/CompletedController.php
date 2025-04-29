<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class CompletedController extends BaseController
{

    public function __construct() {}

        public function updateIndexFile()
{
    try {
        $key = $_POST['key'] ?? null;
        if (!$this->isValidKey($key)) {
            echo json_encode(["success" => false, "message" => "Invalid completion key."]);
            exit;
        }

        // Prepare code snippet and response
        $codeSnippet = $this->getCodeSnippet();
        $message = str_replace(":code", htmlentities($codeSnippet), t('finalized_message', "The installation was successful. You will now be redirected to the main page.<br><br>Before proceeding, the script has executed its final tasks:<br><br><b>1. It attempted to remove the following code segment from the public/index.php file:</b><br><pre style='background: black; color: white; padding: 10px; border-radius: 5px; max-height: 300px; overflow-y: auto;'><code>:code</code></pre><br>Please verify that this code has been successfully deleted.<br><br><b>2. The setup folder has been scheduled for removal. Ensure that it has been fully deleted.</b><br><br>Once these checks are completed, the JSON response will trigger the redirection to the main page."));

        // Send response
        header('Content-Type: application/json');
        echo json_encode([
            "success" => true,
            "redirect" => true,
            "message" => $message,
            "url" => $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/"
        ]);

        // Ensure output is sent
        ob_flush();
        flush();

        // Wait a moment before cleanup
        sleep(1);

        // Then perform cleanup
        $this->performCleanup();

    } catch (\Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            "success" => false,
            "message" => "Error: " . $e->getMessage()
        ]);
    }
    exit;
}

        private function performCleanup()
        {
            $indexPhpPath = __DIR__ . '/../../../index.php';

            // Read and modify index.php
            $content = file_get_contents($indexPhpPath);
            $startMarker = '//// KYNAR NETWORK LARAVEL INSTALLER START ////';
            $endMarker = '//// KYNAR NETWORK LARAVEL INSTALLER END ////';

            $startPos = strpos($content, $startMarker);
            $endPos = strpos($content, $endMarker);

            if ($startPos !== false && $endPos !== false) {
                $lengthToRemove = ($endPos - $startPos) + strlen($endMarker);
                $newContent = substr_replace($content, '', $startPos, $lengthToRemove);
                $newContent = preg_replace('/\n{2,}/', "\n", $newContent);
                $newContent = trim($newContent);
                file_put_contents($indexPhpPath, $newContent);
            }
        }



    private function getCodeSnippet()
    {
        return <<<EOT
//// KYNAR NETWORK LARAVEL INSTALLER START ////

function loadEnv() {
    \$envPath = __DIR__ . '/../.env';
    if (!file_exists(\$envPath)) {
        return [];
    }

    \$lines = file(\$envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    \$envVariables = [];

    foreach (\$lines as \$line) {
        if (strpos(trim(\$line), '#') === 0 || strpos(trim(\$line), '=') === false) continue;
        list(\$name, \$value) = explode('=', \$line, 2);
        \$envVariables[trim(\$name)] = trim(\$value, '"\'');
    }

    return \$envVariables;
}

function env(\$key, \$default = null) {
    static \$envVariables = [];
    if (empty(\$envVariables)) {
        \$envVariables = loadEnv();
    }
    return isset(\$envVariables[\$key]) ? \$envVariables[\$key] : \$default;
}

\$envPath = '../.env';

\$dbConfigured = file_exists(\$envPath) && env('DB_CONNECTION') !== false;
\$appKeySet = env('APP_KEY') !== null;

// Check required Laravel directories and files
\$requiredPaths = [
    '../vendor' => 'Vendor directory missing. Please run composer install.',
    '../storage' => 'Storage directory missing.',
    '../storage/app' => 'Storage app directory missing.',
    '../storage/framework' => 'Storage framework directory missing.',
    '../storage/framework/cache' => 'Storage framework cache directory missing.',
    '../storage/framework/sessions' => 'Storage framework sessions directory missing.',
    '../storage/framework/views' => 'Storage framework views directory missing.',
    '../storage/logs' => 'Storage logs directory missing.'
];

foreach (\$requiredPaths as \$path => \$error) {
    if (!file_exists(__DIR__ . '/' . \$path)) {
        header("Location: /setup/index.php");
        exit;
    }
}

if (!\$dbConfigured || !\$appKeySet) {
    header("Location: /setup/index.php");
    exit;
}

//// KYNAR NETWORK LARAVEL INSTALLER END ////
EOT;
    }
}
