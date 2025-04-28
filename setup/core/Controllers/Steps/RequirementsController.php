<?php

namespace App\Controllers\Steps;

use App\Controllers\BaseController;

class RequirementsController extends BaseController
{
    private array $requirements;
    //private string $rootPath;

    public function __construct()
    {
        parent::__construct();
        $this->rootPath = realpath(__DIR__ . '/../../../../..');
        $this->loadRequirements();
        $this->languageManager = \App\Languages\LanguageManager::getInstance();
    }


    public function handle(string $lang, array $step)
    {
        $currentStep = $this->stepManager->getCurrentStep();
        if (!is_array($currentStep) || !isset($currentStep['id'])) {
            throw new \RuntimeException('Invalid step configuration: Expected array with id key');
        }

        $install_req = $this->stepManager->getNextStep($currentStep, $lang);
        $prevStep = $this->stepManager->getPreviousStep($step, $lang);
        $steps = $this->stepManager->getSteps();
        $currentStepIndex = array_search($currentStep['id'], array_column($steps, 'id'));

        if ($currentStepIndex === false) {
            throw new \RuntimeException('Current step not found in steps configuration');
        }

        $this->requirements['failedChecks'] = $this->calculateFailedChecks();
        $this->requirements['requiredFailedChecks'] = $this->calculateRequiredFailedChecks();
        $this->requirements['vendorFolderPassed'] = $this->requirements['vendorFolderOk'] && $this->requirements['autoloadFileOk'];

        // Determine next step based on checks
        if ($this->requirements['requiredFailedChecks'] === 0 && $this->requirements['failedChecks'] > 0) {
            // Skip dependencies step if only optional checks failed
            $steps = $this->stepManager->getSteps();
            $currentStepIndex = array_search($currentStep['id'], array_column($steps, 'id'));
            $nextStep = $this->stepManager->getNextStep($steps[$currentStepIndex + 1] ?? null, $lang);
        } else {
            // Normal progression
            $nextStep = $this->stepManager->getNextStep($currentStep, $lang);
        }

        return $this->render('install/steps/requirements', [
            'requirements' => $this->requirements,
            'phpVersionOk' => $this->requirements['phpVersionOk'],
            'minPhpVersion' => $this->requirements['minPhpVersion'],
            'extensions' => $this->requirements['extensions'],
            'dotenvFileOk' => $this->requirements['dotenvFileOk'],
            'storageDirOk' => $this->requirements['storageDirOk'],
            'logsDirOk' => $this->requirements['logsDirOk'],
            'frameworkDirOk' => $this->requirements['frameworkDirOk'],
            'framework_cacheDirOk' => $this->requirements['framework_cacheDirOk'],
            'framework_sessionDirOk' => $this->requirements['framework_sessionDirOk'],
            'framework_testingDirOk' => $this->requirements['framework_testingDirOk'],
            'framework_viewsDirOk' => $this->requirements['framework_viewsDirOk'],
            'composerInstalled' => $this->requirements['composerInstalled'],
            'npmInstalled' => $this->requirements['npmInstalled'],
            'vendorFolderOk' => $this->requirements['vendorFolderOk'],
            'autoloadFileOk' => $this->requirements['autoloadFileOk'],
            'vendorFolderPassed' => $this->requirements['vendorFolderPassed'],
            'failedChecks' => $this->requirements['failedChecks'],
            'requiredFailedChecks' => $this->requirements['requiredFailedChecks'],
            'currentStep' => $currentStep,
            'install_req' => $install_req,
            'nextStep' => $nextStep,
            'prevStep' => $prevStep,
            'currentStepIndex' => $currentStepIndex,
            'isFirstStep' => $this->stepManager->isFirstStep($currentStep),
            'stepsConfig' => ['steps' => $steps],
            'key' => $this->getSetupKey()
        ]);
    }

    private function calculateFailedChecks(): int
    {
        $failedChecks = 0;

        if (!$this->requirements['phpVersionOk']) {
            $failedChecks++;
        }

        foreach ($this->requirements['extensions'] as $info) {
            if ($info['status'] === 'Failed') {
                $failedChecks++;
            }
        }

        if (!$this->requirements['dotenvFileOk']) $failedChecks++;
        if (!$this->requirements['storageDirOk']) $failedChecks++;
        if (!$this->requirements['logsDirOk']) $failedChecks++;
        if (!$this->requirements['frameworkDirOk']) $failedChecks++;
        if (!$this->requirements['framework_cacheDirOk']) $failedChecks++;
        if (!$this->requirements['framework_sessionDirOk']) $failedChecks++;
        if (!$this->requirements['framework_testingDirOk']) $failedChecks++;
        if (!$this->requirements['framework_viewsDirOk']) $failedChecks++;
        if (!$this->requirements['composerInstalled']) $failedChecks++;
        if (!$this->requirements['npmInstalled']) $failedChecks++;
        if (!$this->requirements['vendorFolderOk']) $failedChecks++;
        if (!$this->requirements['autoloadFileOk']) $failedChecks++;

        return $failedChecks;
    }

    private function calculateRequiredFailedChecks(): int
    {
        $requiredFailedChecks = 0;

        if (!$this->requirements['phpVersionOk']) {
            $requiredFailedChecks++;
        }

        foreach ($this->requirements['extensions'] as $info) {
            if ($info['status'] === 'Failed' && $info['required']) {
                $requiredFailedChecks++;
            }
        }

        // These are all required checks
        if ($this->requirements['dotenvFileOk'] === false) $requiredFailedChecks++;
        if ($this->requirements['storageDirOk'] === false) $requiredFailedChecks++;
        if ($this->requirements['logsDirOk'] === false) $requiredFailedChecks++;
        if (!$this->requirements['frameworkDirOk']) $requiredFailedChecks++;
        if (!$this->requirements['framework_cacheDirOk']) $requiredFailedChecks++;
        if (!$this->requirements['framework_sessionDirOk']) $requiredFailedChecks++;
        if (!$this->requirements['framework_testingDirOk']) $requiredFailedChecks++;
        if (!$this->requirements['framework_viewsDirOk']) $requiredFailedChecks++;
        if ($this->requirements['composerInstalled'] === false) $requiredFailedChecks++;
        if ($this->requirements['npmInstalled'] === false) $requiredFailedChecks++;
        if ($this->requirements['vendorFolderOk'] === false) $requiredFailedChecks++;
        if ($this->requirements['autoloadFileOk'] === false) $requiredFailedChecks++;

        return $requiredFailedChecks;
    }

    private function loadRequirements(): void
    {
        $minPhpVersion = '8.2';
        $extensionsConfig = $this->loadExtensionsConfig();

        $this->requirements = [
            'phpVersionOk' => version_compare(PHP_VERSION, $minPhpVersion, '>='),
            'minPhpVersion' => $minPhpVersion,
            'extensions' => $this->checkExtensions($extensionsConfig),
            'dotenvFileOk' => file_exists(__DIR__ . '/../../../../../.env') && is_writable(__DIR__ . '/../../../../../.env'),
            'storageDirOk' => is_dir(__DIR__ . '/../../../../../storage') && is_writable(__DIR__ . '/../../../../../storage'),
            'logsDirOk' => is_dir(__DIR__ . '/../../../../../storage/logs') && is_writable(__DIR__ . '/../../../../../storage/logs'),
            'frameworkDirOk' => is_dir(__DIR__ . '/../../../../../storage/framework') && is_writable(__DIR__ . '/../../../../../storage/framework'),
            'framework_cacheDirOk' => is_dir(__DIR__ . '/../../../../../storage/framework/cache') && is_writable(__DIR__ . '/../../../../../storage/framework/cache'),
            'framework_sessionDirOk' => is_dir(__DIR__ . '/../../../../../storage/framework/sessions') && is_writable(__DIR__ . '/../../../../../storage/framework/sessions'),
            'framework_testingDirOk' => is_dir(__DIR__ . '/../../../../../storage/framework/testing') && is_writable(__DIR__ . '/../../../../../storage/framework/testing'),
            'framework_viewsDirOk' => is_dir(__DIR__ . '/../../../../../storage/framework/views') && is_writable(__DIR__ . '/../../../../../storage/framework/views'),
            'composerInstalled' => $this->checkComposerInstalled(),
            'npmInstalled' => $this->checkNpmInstalled(),
            'vendorFolderOk' => is_dir(__DIR__ . '/../../../../../vendor'),
            'autoloadFileOk' => file_exists(__DIR__ . '/../../../../../vendor/autoload.php') && filesize(__DIR__ . '/../../../../../vendor/autoload.php') > 0
        ];

        // Calculate failed checks
        $this->requirements['failedChecks'] = $this->calculateFailedChecks();
        $this->requirements['requiredFailedChecks'] = $this->calculateRequiredFailedChecks();
        $this->requirements['vendorFolderPassed'] = $this->requirements['vendorFolderOk'] && $this->requirements['autoloadFileOk'];
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
            if (function_exists('extension_loaded')) {
                $status = extension_loaded($extInfo['extension_id']) ? 'Passed' : 'Failed';
            } else {
                $status = 'N/A - function_exists() not available';
            }

            // Get translated description using LanguageManager
            $descriptionKey = 'ext_desc.' . $extInfo['extension_id'];
            $description = $this->languageManager->translate($descriptionKey) ??
                "Extension description not found for: {$extInfo['extension_id']}";

            $extensions[$extInfo['extension_id']] = [
                'name' => $extInfo['extension_name'],
                'description' => $description,
                'required' => $extInfo['required'],
                'status' => $status
            ];
        }
        return $extensions;
    }

    private function checkComposerInstalled(): bool
    {
        return function_exists('shell_exec') && shell_exec('where composer') !== null;
    }

    private function checkNpmInstalled(): bool
    {
        return function_exists('shell_exec') && shell_exec('where npm') !== null;
    }
}
