<?php

namespace App\Languages;

class LanguageManager
{
    private static $instance = null;
    private $translations = [];
    private $currentLanguage;
    private $languagesPath;
    private $config;
    private array $slugTranslations = [];
    private array $languages = [];
    private string $selectedLanguage = 'en';
    private array $availableLanguages = [];

    public function __construct()
    {
        $this->languagesPath = realpath(__DIR__ . '/../../languages');
        $this->loadConfig();
        $this->loadAvailableLanguages();
        $this->currentLanguage = $this->getLanguageFromUrl();
        $this->loadLanguage();
    }

    private function loadConfig(): void
    {
        $configFile = realpath(__DIR__ . '/../../configs/general.json');
        if (!file_exists($configFile)) {
            throw new \RuntimeException("Configuration file not found");
        }

        $this->config = json_decode(file_get_contents($configFile), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Error parsing config file: " . json_last_error_msg());
        }
    }

    private function getLanguageFromUrl(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $segments = explode('/', trim($uri, '/'));

        // Find language segment after 'setup'
        $setupIndex = array_search('setup', $segments);
        if ($setupIndex !== false && isset($segments[$setupIndex + 1])) {
            $possibleLang = $segments[$setupIndex + 1];
            // Check if the next segment is not 'step'
            if ($possibleLang !== 'step' && $this->isValidLanguage($possibleLang)) {
                return $possibleLang;
            }
        }

        // Return default language if no valid language found in URL
        return $this->config['default_language'] ?? 'en';
    }

    private function loadAvailableLanguages(): void
    {
        $files = glob($this->languagesPath . DIRECTORY_SEPARATOR . '*.json');
        $this->availableLanguages = [];

        foreach ($files as $file) {
            $langCode = basename($file, '.json');
            $content = json_decode(file_get_contents($file), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $this->availableLanguages[] = [
                    'id' => $langCode,
                    'english_name' => $content['english_name'] ?? $langCode,
                    'native_name' => $content['native_name'] ?? $langCode,
                    'flag_image' => $content['flag_image'] ?? $langCode . '.png'
                ];
            }
        }
    }

    public function isValidLanguage(string $lang): bool
    {
        // Check if the language exists in the available languages array
        foreach ($this->availableLanguages as $language) {
            if ($language['id'] === $lang) {
                return true;
            }
        }
        return false;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setLanguage(string $lang): void
    {
        $this->currentLanguage = $lang;
        $this->loadLanguage();
    }

    private function loadLanguage(): void
    {
        $this->translations = [];  // Reset translations
        $langFile = $this->languagesPath . DIRECTORY_SEPARATOR . $this->currentLanguage . '.json';

        if (!file_exists($langFile)) {
            // Fallback to default language if current language file doesn't exist
            $langFile = $this->languagesPath . DIRECTORY_SEPARATOR . ($this->config['default_language'] ?? 'en') . '.json';
        }

        if (file_exists($langFile)) {
            $content = json_decode(file_get_contents($langFile), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Get translations from the translations object
                $this->translations = $content['translations'] ?? [];
            }
        }
    }

    public function translate(string $key): ?string
    {
        // Force reload language if it changed
        if ($this->currentLanguage !== $this->getLanguageFromUrl()) {
            $this->currentLanguage = $this->getLanguageFromUrl();
            $this->loadLanguage();
        }

        return $this->translations[$key] ?? null;
    }

    public function getAvailableLanguages(): array
    {
        return $this->availableLanguages;
    }

    public function getCurrentLanguage(): string
    {
        // Force re-check of URL language
        $urlLang = $this->getLanguageFromUrl();
        if ($urlLang !== $this->currentLanguage) {
            $this->currentLanguage = $urlLang;
            $this->loadLanguage();
        }
        return $this->currentLanguage;
    }

    public function getDefaultLanguage(): string
    {
        return $this->config['default_language'] ?? 'en';
    }

    public function redirectToLanguageUrl(): void
    {
        // Assuming this method performs some checks and redirects if necessary
        // Ensure it doesn't change the $_SERVER['REQUEST_METHOD']
        $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $segments = explode('/', trim($currentUri, '/'));

        if (count($segments) > 1 && !$this->isValidLanguage($segments[1])) {
            // Perform redirection to a default language URL
            $defaultLang = 'en'; // Example default language
            $newUri = '/' . implode('/', [$segments[0], $defaultLang] + array_slice($segments, 2));
            header("Location: " . $newUri);
            exit;
        }
    }

    private function generateUrlWithLanguage(string $originalUri, string $lang): string
    {
        $parsedUrl = parse_url($originalUri);
        $pathSegments = explode('/', trim($parsedUrl['path'] ?? '', '/'));
        if ($pathSegments[0] === 'setup' && empty($pathSegments[1])) {
            $pathSegments[1] = $lang;
        }
        $newPath = implode('/', $pathSegments);
        return $newPath . ($parsedUrl['query'] ?? '');
    }

    public function generateSlugTranslations(array $stepsConfig): array
    {
        $allTranslations = [];
        foreach ($stepsConfig['steps'] as $step) {
            $allTranslations[$step['id']] = [
                'slug' => $step['slug'],
                'translations' => []
            ];

            foreach ($this->availableLanguages as $lang) {
                $translationKey = 'slug.' . $step['slug'];
                // Get translation from the language file
                $langFile = $this->languagesPath . DIRECTORY_SEPARATOR . $lang['id'] . '.json';

                if (!file_exists($langFile)) {
                    error_log("Language file not found: {$langFile}");
                    continue;
                }

                $translations = json_decode(file_get_contents($langFile), true);

                // Try to get translation from translations object
                $translated = $translations['translations'][$translationKey] ?? null;

                error_log("Getting translation for step '{$step['id']}' with key '{$translationKey}' in language '{$lang['id']}': " . ($translated ?: 'not found'));

                // Store translation or fallback
                $allTranslations[$step['id']]['translations'][$lang['id']] = $translated ?: (
                    $step['translations'][$lang['id']] ?? $step['slug']
                );
            }
        }

        $this->slugTranslations = $allTranslations;
        return $allTranslations;
    }

    public function getSlugTranslations(): array
    {
        return $this->slugTranslations;
    }

    public function setCurrentLanguage(string $languageId): void
    {
        error_log("Setting language to: " . $languageId);
        $this->selectedLanguage = $languageId;
    }

     /**
     * Get text direction for current language
     */
    public function getTextDirection(): string
    {
        $langFile = $this->languagesPath . DIRECTORY_SEPARATOR . $this->getCurrentLanguage() . '.json';
        if (file_exists($langFile)) {
            $langData = json_decode(file_get_contents($langFile), true);
            return $langData['direction'] ?? 'ltr';
        }
        return 'ltr'; // Default to left-to-right
    }
}
