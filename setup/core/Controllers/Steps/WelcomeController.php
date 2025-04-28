<?php

namespace App\Controllers\Steps;

use App\Controllers\BaseController;

class WelcomeController extends BaseController
{
    public function handle(string $lang, array $step)
    {
        // Set the current language
        $this->languageManager->setLanguage($lang);

        // Get step navigation data
        $nextStep = $this->stepManager->getNextStep($step, $lang);
        $prevStep = $this->stepManager->getPreviousStep($step, $lang);

        return $this->render($step['template'], [
            'currentStep' => $step,
            'nextStep' => $nextStep,
            'prevStep' => $prevStep,
            'isFirstStep' => $this->stepManager->isFirstStep($step)
        ]);
    }
}
