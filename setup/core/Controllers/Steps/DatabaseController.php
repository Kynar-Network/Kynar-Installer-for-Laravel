<?php

namespace App\Controllers\Steps;

use App\Controllers\BaseController;
use App\Services\DatabaseService;
use App\Utils\Dotenv;
use Exception;

class DatabaseController extends BaseController
{
    private DatabaseService $databaseService;
    private array $dbConfig;
    private bool $showMigrationSection = false;
    private array $availableDrivers = ['sqlite', 'mysql', 'mariadb', 'pgsql', 'sqlsrv'];

    public function __construct()
    {
        parent::__construct();
        require_once $this->getRootPath() . '/vendor/autoload.php';
        $this->databaseService = new DatabaseService();
        $this->loadConfiguration();
    }

    private function loadConfiguration(): void
    {
        try {
            $dotenv = Dotenv::createImmutable($this->getRootPath());
            $dotenv->safeLoad();

            $this->dbConfig = [
                'driver' => $this->getEnv('DB_CONNECTION', 'mysql'),
                'host' => $this->getEnv('DB_HOST', '127.0.0.1'),
                'port' => $this->getEnv('DB_PORT', 3306),
                'database' => $this->getEnv('DB_DATABASE', ''),
                'username' => $this->getEnv('DB_USERNAME', ''),
                'password' => $this->getEnv('DB_PASSWORD', '')
            ];
        } catch (Exception $e) {
            // Initialize with default values if .env loading fails
            $this->dbConfig = [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => 3306,
                'database' => '',
                'username' => '',
                'password' => ''
            ];
        }
    }

    private function getEnv(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }

    public function handle(string $lang, array $step)
    {
        $currentStep = $this->stepManager->getCurrentStep();
        $steps = $this->stepManager->getSteps();
        $currentStepIndex = array_search($currentStep['id'], array_column($steps, 'id'));
        $nextStep = $this->stepManager->getNextStep($currentStep, $lang);
        $prevStep = $this->stepManager->getPreviousStep($steps[$currentStepIndex - 1] ?? null, $lang);
        $message = '';
        $messageType = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_and_save'])) {
            try {
                $this->handleDatabaseSubmission($_POST);
                $message = t('database_connection_success', 'Database connection successful!');
                $messageType = 'success';
            } catch (\Exception $e) {
                $message = $e->getMessage();
                $messageType = 'error';
            }
        }

        // Get current language and step URL
        $currentLanguage = $this->languageManager->getCurrentLanguage();
        $selectedLanguageId = $currentLanguage['id'] ?? 'en';
        $currentStepUrl = $this->stepManager->getStepUrl($currentStep, $selectedLanguageId);
        $migrateurl = generateUrl('setup.migrate.stream', [
            'key' => urlencode($this->getSetupKey()),
            'lang' => $lang,
        ]);


        return $this->render($step['template'], [
            'currentStep' => $currentStep,
            'nextStep' => $nextStep,
            'prevStep' => $prevStep,
            'dbConfig' => $this->dbConfig,
            'showMigrationSection' => $this->showMigrationSection,
            'drivers' => $this->availableDrivers,
            'migrationCompleted' => false,
            'message' => $message,
            'messageType' => $messageType,
            'selectedLanguageId' => $selectedLanguageId,
            'currentStepUrl' => $currentStepUrl,
            'migrateurl' => $migrateurl,
        ]);
    }

    private function handleDatabaseSubmission(array $postData): void
    {
        $dbDriver = $postData['db_driver'] ?? '';
        $dbName = $postData['db_name'] ?? '';

        if ($dbDriver === 'sqlite') {
            $this->handleSqliteSubmission($dbName);
        } else {
            $this->handleOtherDbSubmission($postData);
        }
    }

    private function handleSqliteSubmission(string $dbName): void
    {
        if (empty($dbName)) {
            $dbName = 'database/database.sqlite';
        }

        $dbName = $this->normalizeSqlitePath($dbName);

        if ($this->databaseService->ensureSQLiteFileExists($dbName)) {
            if ($this->databaseService->testConnection('sqlite', '', '', $dbName, '', '')) {
                $this->databaseService->updateEnvFile([
                    'DB_CONNECTION' => 'sqlite',
                    'DB_DATABASE' => $dbName
                ]);
                $this->showMigrationSection = true;
            }
        }
    }

    private function handleOtherDbSubmission(array $postData): void
    {
        $dbDriver = $postData['db_driver'];
        $dbHost = $postData['db_host'] ?? '';
        $dbPort = $postData['db_port'] ?? '';
        $dbName = $postData['db_name'] ?? '';
        $dbUsername = $postData['db_username'] ?? '';
        $dbPassword = $postData['db_password'] ?? '';

        // Validate all required fields
        if (empty($dbDriver) || empty($dbHost) || empty($dbPort) || empty($dbName) || empty($dbUsername)) {
            throw new \RuntimeException(t('all_fields_required', 'All fields are required'));
        }

        // Validate database name format for non-SQLite connections
        if ($dbDriver !== 'sqlite' && strpos($dbName, '/') !== false) {
            throw new \RuntimeException(t('invalid_database_name', 'Invalid database name format'));
        }

        // Test the connection
        if (!$this->databaseService->testConnection($dbDriver, $dbHost, $dbPort, $dbName, $dbUsername, $dbPassword)) {
            throw new \RuntimeException(t('database_connection_failed', 'Could not connect to database. Please check your credentials.'));
        }

        // If connection successful, update env file
        $this->databaseService->updateEnvFile([
            'DB_CONNECTION' => $dbDriver,
            'DB_HOST' => $dbHost,
            'DB_PORT' => $dbPort,
            'DB_DATABASE' => $dbName,
            'DB_USERNAME' => $dbUsername,
            'DB_PASSWORD' => $dbPassword
        ]);

        $this->showMigrationSection = true;
    }

    private function normalizeSqlitePath(string $path): string
    {
        if (!preg_match('/\.sqlite$/', $path)) {
            $path .= '.sqlite';
        }

        if (strpos($path, '/') === false && strpos($path, '\\') === false) {
            $path = 'database/' . $path;
        }

        return $path;
    }

    protected function getPreviousStep(): ?array
{
    $currentStep = $this->stepManager->getCurrentStep();
    if (!$currentStep) {
        return null;
    }

    // Get all steps
    $steps = $this->stepManager->getSteps();

    // Find current step index
    $currentIndex = array_search($currentStep, $steps);
    if ($currentIndex === false || $currentIndex < 2) {
        // If we're at the first or second step, return the first step
        return $currentIndex === 1 ? $steps[0] : null;
    }

    // Return step that is two positions before current
    return $steps[$currentIndex - 2];
}

}
