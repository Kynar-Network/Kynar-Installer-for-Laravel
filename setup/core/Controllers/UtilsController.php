<?php

namespace App\Controllers;

class UtilsController extends BaseController
{
    public function create_env(): void
    {
        header('Content-Type: application/json');

        // Retrieve the key from $_POST
        if (!isset($_POST['key'])) {
            echo json_encode(['success' => false, 'message' => 'Key is missing or invalid']);
            return;
        }

        $key = $_POST['key'];

        // Validate the provided key
        if (!$this->isValidKey($key)) {
            echo json_encode(['success' => false, "message" => "Invalid completion key."]);
            return;
        }
        // Generate default .env content
        $envFileContent = <<<'ENV'
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_PASSWORD=

FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
ENV;

        // Save to .env file
        $envFilePath = __DIR__ . '/../../../../.env';
        if (file_put_contents($envFilePath, $envFileContent) !== false) {
            echo json_encode(['success' => true, 'message' => 'Environment created']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create environment']);
        }
    }

    public function install_manual()
    {
        // Navigate 3 folders up
        $threeFoldersUp = getParentFolder(__FILE__, 5);
        $currentLanguage = $this->languageManager->getCurrentLanguage();
        $selectedLanguageId = $currentLanguage['id'] ?? 'en';
        $selectedLanguageDirection = $currentLanguage['direction'] ?? 'ltr';

        return $this->render('install/manual-dependencies', [
            'FoldersUp' => $threeFoldersUp,
            'selectedLanguageDirection' => $selectedLanguageDirection,
            'selectedLanguageId' => $selectedLanguageId
        ], 'manual');
    }

      /**
     * Get the base URL for assets
     */
    public function getAssetsUrl(): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . $host . '/setup/assets';
    }

    /**
     * Generate asset URL
     */
    public function asset(string $path): string
    {
        // Remove leading slash if present
        $path = ltrim($path, '/');
        return $this->getAssetsUrl() . '/' . $path;
    }
}
