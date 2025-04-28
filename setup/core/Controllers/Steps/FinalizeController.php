<?php

namespace App\Controllers\Steps;

use App\Controllers\BaseController;
use App\Traits\LaravelBootstrap;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FinalizeController extends BaseController
{
    use LaravelBootstrap;
    private array $requirements = [];

    public function __construct()
    {
        parent::__construct();
        $this->bootLaravel();
    }

    public function handle(string $lang, array $step)
    {

        // Initialize requirements first
        $this->initializeRequirements();

        // Get user information
        $createdUser = $this->getCreatedUser();

        // Get database information
        $databaseInfo = $this->getDatabaseInfo();

        // Get environment variables
        $envVars = $this->getFilteredEnvVars();

        $currentLanguage = $this->languageManager->getCurrentLanguage();
        $selectedLanguageId = $currentLanguage['id'] ?? 'en';


        return $this->render($step['template'], [
            'user' => $createdUser,
            'requirements' => $this->requirements,
            'database' => $databaseInfo,
            'env_vars' => $envVars,
            'currentStep' => $this->stepManager->getCurrentStep(),
            'prev_step' => $this->stepManager->getPreviousStep($this->stepManager->getCurrentStep()),
            'setupKey' => $this->getSetupKey(),
            'lang' => $selectedLanguageId,
            'completeurl' => generateUrl('setup.complete'),
            'key' => $this->getSetupKey()
        ]);
    }

    private function getCreatedUser(): array
    {
        if (Schema::hasTable('users')) {
            $user = DB::table('users')->orderBy('created_at', 'desc')->first();
            return [
                'username' => $user ? $user->name : '',
                'email' => $user ? $user->email : '',
                'password' => '*******'
            ];
        } else {
            // Table doesn't exist yet, return empty user data
            return [
                'username' => '',
                'email' => '',
                'password' => '*******'
            ];
        }
    }

    private function getDatabaseInfo(): array
    {
        try {
            $dbDriver = $_ENV['DB_CONNECTION'] ?? 'mysql';
            $dbName = $_ENV['DB_DATABASE'] ?? '';

            // Get table names based on database driver
            if ($dbDriver === 'sqlite') {
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%';");
                $tableNames = array_map(function ($table) {
                    return $table->name;
                }, $tables);
            } else {
                // For MySQL/PostgreSQL
                $tables = DB::select('SHOW TABLES');
                $columnName = "Tables_in_" . $dbName;
                $tableNames = array_map(function ($table) use ($columnName) {
                    return $table->$columnName;
                }, $tables);
            }

            return [
                'driver' => $dbDriver,
                'name' => $dbName,
                'tables' => $tableNames
            ];
        } catch (\Exception $e) {
            return [
                'driver' => $dbDriver ?? 'unknown',
                'name' => $dbName ?? 'unknown',
                'tables' => []
            ];
        }
    }


    private function getFilteredEnvVars(): array
    {
        $sensitiveVars = ['DB_PASSWORD', 'AWS_SECRET_ACCESS_KEY', 'MAIL_PASSWORD', 'token'];
        return array_filter($_ENV, function ($key) use ($sensitiveVars) {
            return !in_array($key, $sensitiveVars);
        }, ARRAY_FILTER_USE_KEY);
    }

    private function initializeRequirements(): void
    {
        // Load extensions configuration
        $extensionsConfig = $this->loadExtensionsConfig();

        // Initialize requirements array
        $this->requirements = [
            'php_version' => [
                'status' => version_compare(PHP_VERSION, '8.2.0', '>='),
                'version' => PHP_VERSION,
                'required' => true
            ],
            'extensions' => $this->checkExtensions($extensionsConfig),
            'env_file' => [
                'status' => file_exists($this->getRootPath() . '/.env'),
                'required' => true
            ],
            'storage_dir' => [
                'status' => is_writable($this->getRootPath() . '/storage'),
                'required' => true
            ],
            'logs_dir' => [
                'status' => is_writable($this->getRootPath() . '/storage/logs'),
                'required' => true
            ]
        ];
    }

    private function loadExtensionsConfig(): array
    {
        $extensionsFilePath = realpath(__DIR__ . '/../../../configs/extensions.json');
        if (!$extensionsFilePath || !file_exists($extensionsFilePath)) {
            throw new \RuntimeException('Extensions configuration file not found');
        }

        $config = json_decode(file_get_contents($extensionsFilePath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON in extensions configuration');
        }

        return $config['extensions'] ?? [];
    }

    private function checkExtensions(array $extensionsConfig): array
    {
        $extensions = [];
        foreach ($extensionsConfig as $extInfo) {
            $status = extension_loaded($extInfo['extension_id']) ? 'Passed' : 'Failed';
            $extensions[$extInfo['extension_id']] = [
                'name' => $extInfo['extension_name'],
                'description' => $extInfo['extension_description'],
                'required' => $extInfo['required'],
                'status' => $status
            ];
        }
        return $extensions;
    }
}
