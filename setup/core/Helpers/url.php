<?php

/**
 * Generate a URL for a named route with automatic language parameter handling
 */
if (!function_exists('generateUrl')) {
    function generateUrl(string $name, array $parameters = [], bool $isStep = false): string
    {
        global $router;

        if (!isset($parameters['lang'])) {
            // Get language manager instance
            $languageManager = \App\Languages\LanguageManager::getInstance();
            $currentLanguage = $languageManager->getCurrentLanguage();
            $parameters['lang'] = $currentLanguage ?? 'en';
        }

        // Handle step URLs with translations
        if ($isStep && isset($parameters['stepid'])) {
            // Load steps configuration
            $stepsConfig = json_decode(file_get_contents(__DIR__ . '/../../configs/steps.json'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException("Invalid JSON in steps config: " . json_last_error_msg());
            }

            // Find step by ID
            $step = null;
            foreach ($stepsConfig['steps'] as $stepConfig) {
                if ($stepConfig['id'] === $parameters['stepid']) {
                    $step = $stepConfig;
                    break;
                }
            }

            if (!$step) {
                throw new \RuntimeException("Step not found with ID: {$parameters['stepid']}");
            }

            // Get translated slug
            $translationKey = 'slug.' . $step['slug'];
            $translatedSlug = t($translationKey, null, ['lang' => $parameters['lang']]) ?: $step['slug'];

            // Replace stepid with translated slug in parameters
            unset($parameters['stepid']);
            $parameters['slug'] = $translatedSlug;

            // Build URL manually for steps
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'];
            $url = $protocol . $host . '/setup/' . $parameters['lang'] . '/step/' . $parameters['slug'];

            error_log("Generated step URL: " . $url);
            return $url;
        }

        try {
            return $router->url($name, $parameters);
        } catch (\Exception $e) {
            error_log("URL generation failed: " . $e->getMessage());
            return '/';
        }
    }
}

if (!function_exists('asset')) {
    /**
     * Generate an asset path
     */
    function asset(string $path): string
    {
        static $utilsController = null;
        if ($utilsController === null) {
            $utilsController = new \App\Controllers\UtilsController();
        }
        return $utilsController->asset($path);
    }
}
