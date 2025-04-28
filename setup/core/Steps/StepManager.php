<?php

namespace App\Steps;

use App\Languages\LanguageManager;

class StepManager
{
    private array $steps = [];
    private string $currentStep;
    private string $currentStepSlug;
    private LanguageManager $languageManager;

    public function __construct()
    {
        $this->languageManager = LanguageManager::getInstance();
        $this->loadSteps();
        $this->currentStep = $this->getCurrentStepFromUrl();
        $this->currentStepSlug = $this->determineCurrentStep();
    }

    public function getStepBySlug(string $slug): ?array
    {
        foreach ($this->steps as $step) {
            $translatedSlug = t($step['slug']) ?: $step['slug_default'];
            if ($translatedSlug === $slug) {
                return $step;
            }
        }
        return null;
    }

    public function getStepStatus(array $step): string
    {
        $currentStepIndex = $this->getStepIndexById($this->currentStep);
        $stepIndex = $this->getStepIndexById($step['id']);

        if ($step['id'] === $this->currentStep) {
            return 'current';
        }

        return $stepIndex < $currentStepIndex ? 'completed' : 'pending';
    }

    public function getStepsWithStatus(): array
    {
        return array_map(function ($step) {
            return array_merge($step, [
                'status' => $this->getStepStatus($step)
            ]);
        }, $this->steps);
    }

    private function loadSteps(): void
    {
        $json = file_get_contents(__DIR__ . '/../../configs/steps.json');
        $data = json_decode($json, true);
        $this->steps = $data['steps'];
    }

    private function getStepIndexById(string $stepId): int
    {
        foreach ($this->steps as $index => $step) {
            if ($step['id'] === $stepId) {
                return $index;
            }
        }
        return -1;
    }

    public function getNextStep($current, ?string $languageId = null): ?array
    {
        $currentId = is_array($current) ? $current['id'] : $current;
        $currentIndex = $this->getStepIndexById($currentId);
        $nextStep = $this->steps[$currentIndex + 1] ?? null;

        if ($nextStep) {
            // Use current language if none provided
            $lang = $languageId ?? $this->languageManager->getCurrentLanguage();
            $nextStep['url'] = $this->getStepUrl($nextStep, $lang);
            // Also store the language for reference
            $nextStep['language'] = $lang;
        }

        return $nextStep;
    }

    public function getPreviousStep($current, ?string $languageId = null): ?array
    {
        $currentId = is_array($current) ? $current['id'] : $current;
        $currentIndex = $this->getStepIndexById($currentId);
        $prevStep = $currentIndex > 0 ? $this->steps[$currentIndex - 1] : null;

        if ($prevStep) {
            // Use current language if none provided
            $lang = $languageId ?? $this->languageManager->getCurrentLanguage();
            $prevStep['url'] = $this->getStepUrl($prevStep, $lang);
            // Also store the language for reference
            $prevStep['language'] = $lang;
        }

        return $prevStep;
    }

    public function isFirstStep($step): bool
    {
        return is_array($step) ?
            $step['id'] === $this->steps[0]['id'] :
            $step === $this->steps[0]['slug'];
    }

    public function getCurrentStep(): array
    {
        foreach ($this->steps as $step) {
            if ($step['id'] === $this->currentStep) {
                return $step;
            }
        }

        // Return the first step if current step not found
        return $this->steps[0];
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    /**
     * Get the URL for a specific step
     */
    public function getStepUrl(array $step, ?string $languageId = null): string
    {
        // Use provided language ID or get current language from LanguageManager
        $lang = $languageId ?? $this->languageManager->getCurrentLanguage();

        if (!isset($step['slug'])) {
            return '/setup/' . $lang;
        }

        // Get the translated slug using the correct key format
        $translationKey = 'slug.' . $step['slug'];
        $translatedSlug = t($translationKey) ?: $step['slug_default'];

        // URL encode the translated slug to handle non-ASCII characters
        $encodedSlug = urlencode($translatedSlug);

        return '/setup/' . $lang . '/step/' . $encodedSlug;
    }

    private function getCurrentStepFromUrl(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $segments = explode('/', trim($uri, '/'));
        $lastSegment = end($segments);

        // If we're at the root of /setup/, treat as welcome step
        if (empty($segments) || ($segments[0] === 'setup' && count($segments) === 1)) {
            return 'welcome';
        }

        // Try to find step by slug or slug_default
        foreach ($this->steps as $step) {
            $translationKey = 'slug.' . $step['slug'];
            $translatedSlug = t($translationKey) ?: $step['slug_default'];
            // URL decode the last segment to compare with translated slug
            if ($translatedSlug === urldecode($lastSegment)) {
                return $step['id'];
            }
        }

        return 'welcome'; // Default step
    }

    private function determineCurrentStep(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $segments = explode('/', trim($uri, '/'));

        // Look for step in URL
        $stepIndex = array_search('step', $segments);
        if ($stepIndex !== false && isset($segments[$stepIndex + 1])) {
            $requestedSlug = urldecode($segments[$stepIndex + 1]);

            // Get the slug from the step ID or translated slug
            foreach ($this->steps as $step) {
                $translationKey = 'slug.' . $step['slug'];
                $translatedSlug = t($translationKey) ?: $step['slug_default'];
                if ($translatedSlug === $requestedSlug) {
                    return $step['slug'];
                }
            }
        }

        // Return first step's translated slug if no step found in URL
        $firstStep = $this->steps[0];
        $translationKey = 'slug.' . $firstStep['slug'];
        return t($translationKey) ?: $firstStep['slug_default'];
    }

    public function getStepById(string $id): ?array
    {
        foreach ($this->steps as $step) {
            if ($step['id'] === $id) {
                return $step;
            }
        }
        return null;
    }
}
