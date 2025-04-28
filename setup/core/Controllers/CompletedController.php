<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class CompletedController extends BaseController
{

    public function __construct() {}

    public function updateIndexFile()
    {
        $key = $_POST['key'] ?? null;
        // Validate the provided key
        if (!$this->isValidKey($key)) {
            return json_encode(["redirect" => false, "message" => "Invalid completion key."]);
        }


        // Path to public/index.php relative to complete.php’s location
        $indexPhpPath = __DIR__ . '/../../../index.php';

        // Read the current content of index.php
        $content = file_get_contents($indexPhpPath);

        if ($content === false) {
            return json_encode(["redirect" => false, "message" => "Error: Unable to load the file."]);
        }

        // Define the start and end markers
        $startMarker = '//// KYNAR NETWORK LARAVEL INSTALLER START ////';
        $endMarker = '//// KYNAR NETWORK LARAVEL INSTALLER END ////';

        // Find the positions of the start and end markers
        $startPos = strpos($content, $startMarker);
        $endPos = strpos($content, $endMarker);

        if ($startPos === false || $endPos === false) {
            return json_encode(["redirect" => false, "message" => "Error: Marker comments not found in index.php."]);
        }

        // Calculate the length of the block to remove
        $lengthToRemove = ($endPos - $startPos) + strlen($endMarker);

        // Remove the block of code including the markers
        $newContent = substr_replace($content, '', $startPos, $lengthToRemove);

        // Optionally, clean up any extra newlines that may have been left behind
        $newContent = preg_replace('/\n{2,}/', "\n", $newContent);

        // Trim leading and trailing newlines
        $newContent = trim($newContent);

        // Write the modified content back to index.php
        if (file_put_contents($indexPhpPath, $newContent) === false) {
            // Handle error (e.g., show a message)
            return json_encode(["redirect" => false, "message" => "Error updating index.php. Check file permissions."]);
        }

        // Path to the setup folder
        $setupFolderPath = __DIR__ . '/../../../../setup';

        // Check if shell_exec is available
        if (function_exists('shell_exec')) {
            // Use shell_exec to delete the setup folder
            shell_exec("rm -rf " . escapeshellarg($setupFolderPath));
        } elseif (function_exists('exec')) {
            // Use exec to delete the setup folder
            exec("rm -rf " . escapeshellarg($setupFolderPath));
        } elseif (function_exists('system')) {
            // Use system to delete the setup folder
            system("rm -rf " . escapeshellarg($setupFolderPath));
        } else {
            // Fallback to PHP functions to delete the setup folder
            $this->deleteDirectory($setupFolderPath);
        }

        $codeSnippet = <<<EOT
        //// KYNAR NETWORK LARAVEL INSTALLER START ////
        
        function loadEnv() {
            \$envPath = __DIR__ . '/../.env';
            if (!file_exists(\$envPath)) {
                return [];
            }
        
            \$lines = file(\$envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            \$envVariables = [];
        
            foreach (\$lines as \$line) {
                if (strpos(trim(\$line), '#') === 0 || strpos(trim(\$line), '=') === false) continue; // Skip comments and invalid lines
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
        
        if (!\$dbConfigured || !\$appKeySet) {
            header("Location: /setup/index.php");
            exit;
        }
        
        //// KYNAR NETWORK LARAVEL INSTALLER END ////
        EOT;
        
                $message = str_replace(":code", htmlentities($codeSnippet), t('finalized_message', 'The installation was successful. You will now be redirected to the main page.<br><br>Before proceeding, the script has executed its final tasks:<br><br><b>1. It attempted to remove the following code segment from the public/index.php file:</b><br><pre style="background: black; color: white; padding: 10px; border-radius: 5px; max-height: 300px; overflow-y: auto;"><code>:code</code></pre><br>Please verify that this code has been successfully deleted.<br><br><b>2. The setup folder has been scheduled for removal. Ensure that it has been fully deleted.</b><br><br>Once these checks are completed, the JSON response will trigger the redirection to the main page.'));
                return json_encode([
                    "redirect" => true,
                    "message" => $message,
                    "url" => $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/"
                ]);
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }
}
