<?php

namespace App\Controllers\Steps;

use App\Controllers\BaseController;

class DependenciesController extends BaseController
{
    protected string $rootPath;
    private bool $composerInstalled;
    private bool $npmInstalled;

    public function __construct()
    {
        parent::__construct();
        $this->rootPath = realpath(__DIR__ . '/../../../../..');
        $this->checkDependencies();
    }

    private function checkDependencies(): void
    {
        $this->composerInstalled = function_exists('shell_exec') && shell_exec('where composer') !== null;
        $this->npmInstalled = function_exists('shell_exec') && shell_exec('where npm') !== null;

        if (!$this->npmInstalled || !$this->composerInstalled) {
            // Redirect to manual installation page
            header('Location: ' . generateUrl('setup.install_manual', ['lang' => $this->languageManager->getCurrentLanguage()]));
            exit;
        }

        $frameworkdir = is_dir(__DIR__ . '/../../../../../storage/framework') && is_writable(__DIR__ . '/../../../../../storage/framework');
        $framework_cacheDir = is_dir(__DIR__ . '/../../../../../storage/framework/cache') && is_writable(__DIR__ . '/../../../../../storage/framework/cache');
        $framework_sessionDirOk = is_dir(__DIR__ . '/../../../../../storage/framework/sessions') && is_writable(__DIR__ . '/../../../../../storage/framework/sessions');
        $framework_testingDirOk = is_dir(__DIR__ . '/../../../../../storage/framework/testing') && is_writable(__DIR__ . '/../../../../../storage/framework/testing');
        $framework_viewsDirOk = is_dir(__DIR__ . '/../../../../../storage/framework/views') && is_writable(__DIR__ . '/../../../../../storage/framework/views');

        if (!$frameworkdir || !$framework_cacheDir || !$framework_sessionDirOk || !$framework_testingDirOk || !$framework_viewsDirOk) {
            // Redirect to manual installation page
            header('Location: ' . generateUrl('step', ['stepid' => 'requirements'], true));
            exit;
        }
    }

    public function handle(string $lang, array $step)
    {
        // Debug log the input parameters
        error_log('Step template: ' . ($step['template'] ?? 'null'));
        error_log('Language: ' . $lang);

        $currentStep = $this->stepManager->getCurrentStep();
        $nextStep = $this->stepManager->getNextStep($currentStep, $lang);
        $prevStep = $this->stepManager->getPreviousStep($currentStep, $lang);

        // Generate params for URL
        $urlParams = [
            'key' => urlencode($this->getSetupKey()),
            'lang' => $lang,
        ];

        // Generate URL with debug
        $installDependenciesUrl = generateUrl('setup.dependencies.stream', $urlParams);

        // Debug log all variables being passed to template
        $templateVars = [
            'currentStep' => $currentStep,
            'nextStep' => $nextStep,
            'prevStep' => $prevStep,
            'composerInstalled' => $this->composerInstalled,
            'npmInstalled' => $this->npmInstalled,
            'installDependenciesUrl' => $installDependenciesUrl
        ];

        // Log each variable's value
        foreach ($templateVars as $key => $value) {
            error_log("Template var '{$key}': " . (is_array($value) ? json_encode($value) : (string)$value));
        }

        try {
            return $this->render($step['template'], $templateVars);
        } catch (\Exception $e) {
            error_log('Render error: ' . $e->getMessage());
            throw $e;
        }
    }
}
