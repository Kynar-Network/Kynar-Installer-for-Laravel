<?php

namespace App\Services;

class MigrationService
{
    private string $rootPath;

    public function __construct()
    {
        $this->rootPath = realpath(__DIR__ . '/../../../..');
    }

    public function streamMigration(): void
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        // Change to the project root directory
        chdir($this->rootPath);

        $command = 'php artisan migrate --force 2>&1';
        $handle = popen($command, 'r');

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                echo "data: " . $line . "\n\n";
                ob_flush();
                flush();
            }
            pclose($handle);
            echo "data: " . t('migrate_success', 'Migrations completed successfully') . "\n\n";
        } else {
            echo "data: " . t('migrate_failed', '❌ Failed to start migration process. Please check your database connection settings.') . "\n\n";
        }
    }
}
