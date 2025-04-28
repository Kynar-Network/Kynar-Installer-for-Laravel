<?php
namespace App\Controllers;

use App\Steps\StepManager;

class InstallController extends BaseController
{
    protected StepManager $stepManager;

    public function __construct()
    {
        parent::__construct();
        $this->stepManager = new StepManager();
    }

    public function index(string $lang = 'en')
    {
        return $this->step('welcome', $lang);
    }

    protected function step(string $stepId, string $lang)
    {
        // Get step configuration
        $step = $this->stepManager->getStepById($stepId);
        if (!$step) {
            throw new \RuntimeException("Step not found: {$stepId}");
        }

        // Set the language
        $this->languageManager->setLanguage($lang);

        // Get controller class name
        $controllerClass = $step['controller'];

        // Create controller instance
        $controller = new $controllerClass();

        // Call handle method with required parameters
        return $controller->handle($lang, $step);
    }
}
