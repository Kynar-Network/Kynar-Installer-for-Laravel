<?php

namespace App\Controllers;

use App\Services\DependencyService;

class DependenciesStreamController extends BaseController
{
    private DependencyService $dependencyService;

    public function __construct()
    {
        parent::__construct();
        $this->dependencyService = new DependencyService();
    }

    public function handle($lang, $key): void
    {
        $keydec = urldecode($key);
        // Validate the provided key
        if (!$this->isValidKey($keydec)) {
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no');

            echo "data: " . json_encode([
                'type' => 'message',
                'message' => t('invalid_key_message', '❌ Invalid completion key.')
            ]) . "\n\n";


            exit;
        }

        $this->dependencyService->streamInstallation();
        exit;
    }
}
