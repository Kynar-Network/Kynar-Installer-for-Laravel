<?php

namespace App\Controllers\Steps;

use App\Controllers\BaseController;
use App\Services\WebsiteService;
use App\Utils\Dotenv;

class WebsiteController extends BaseController
{
    private WebsiteService $websiteService;

    public function __construct()
    {
        parent::__construct();
        $this->websiteService = new WebsiteService();
    }

    public function handle(string $lang, array $step)
    {
        // Load current .env file
        $this->loadEnvFile();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleFormSubmission();
            return;
        }

        // Check and generate APP_KEY if needed
        $this->websiteService->ensureAppKey();

        // Get all environment variables
        $envVars = $this->websiteService->getWebsiteEnvVars();
        $logVars = $this->websiteService->getLogEnvVars();
        $emailVars = $this->websiteService->getEmailEnvVars();
        $awsVars = $this->websiteService->getAwsEnvVars();
        $cacheVars = $this->websiteService->getCacheQueueVars();
        $mailerOptions = $this->websiteService->getMailerOptions();

        // Get current environment values
        $currentEnv = $_ENV;

        $currentStep = $this->stepManager->getCurrentStep();
        $nextStep = $this->stepManager->getNextStep($currentStep, $lang);
        $prevStep = $this->stepManager->getPreviousStep($currentStep, $lang);

        // Render the view with data
        return // Render the view with all sections
            $this->render($step['template'], [
                'env_vars' => $envVars,
                'log_vars' => $logVars,
                'email_vars' => $emailVars,
                'aws_vars' => $awsVars,
                'cache_vars' => $cacheVars,
                'mailer_options' => $mailerOptions,
                'current_env' => $_ENV,
                'currentStep' => $currentStep,
                'prevStep' => $prevStep,
                'nextStep' => $nextStep
            ]);
    }

    private function loadEnvFile(): void
    {
        try {
            $envPath = $this->getRootPath();
            $dotenv = Dotenv::createImmutable($envPath);
            $dotenv->load();
        } catch (\Exception $e) {
            $this->addError('Error loading .env file: ' . $e->getMessage());
        }
    }

    private function handleFormSubmission(): void
    {
        try {
            $formData = $_POST;

            // Validate and sanitize form data
            foreach ($formData as $key => $value) {
                // Skip empty values unless they're meant to be empty
                if ($value === '' && !in_array($key, ['AWS_BUCKET', 'AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY'])) {
                    continue;
                }

                // Save to .env file
                $this->websiteService->setEnvValue($key, $value);
            }

            // Reload environment after changes
            $this->loadEnvFile();

            $this->addSuccess(t('website_config_saved', 'Website configuration saved successfully.'));

            // Get next step and redirect
            $currentStep = $this->stepManager->getCurrentStep();
            $nextStep = $this->stepManager->getNextStep($currentStep);
            header('Location: ' . htmlspecialchars($nextStep['url']));
            exit();

        } catch (\Exception $e) {
            $this->addError('Error saving configuration: ' . $e->getMessage());
            header('Location: ' . generateUrl('step', ['stepid' => 'configuration'], true));
            exit();
        }
    }
    protected function addError(string $message): void
    {
        $_SESSION['errors'][] = $message;
    }

    protected function addSuccess(string $message): void
    {
        $_SESSION['success'][] = $message;
    }
}
