<?php

namespace App\Controllers;

use App\Services\MigrationService;

class MigrateStreamController extends BaseController
{
    private MigrationService $migrationService;

    public function __construct()
    {
        parent::__construct();
        $this->migrationService = new MigrationService();
    }

    public function handle($lang, $key): void
    {
        $keydecoded = urldecode($key);
        if (!$this->isValidKey($keydecoded)) {
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no');

            echo "data: " . t('invalid_key_message', '❌ Invalid completion key.') . "\n\n";

            exit;
        }

        $this->migrationService->streamMigration();
        exit;
    }
}
