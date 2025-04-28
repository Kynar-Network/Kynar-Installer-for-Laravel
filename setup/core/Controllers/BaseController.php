<?php

namespace App\Controllers;

use App\View\View;
use App\Steps\StepManager;
use App\Languages\LanguageManager;

class BaseController
{
    protected View $view;
    protected StepManager $stepManager;
    protected LanguageManager $languageManager;
    protected string $rootPath;
    protected $encryptionKey;
    protected $setupKey;
    protected $originalSetupKey; // Declare the property to avoid deprecation warnings

    public function __construct()
    {
        $this->view = new View();
        $this->stepManager = new StepManager();
        $this->languageManager = LanguageManager::getInstance();
        $this->rootPath = $this->determineRootPath(); // Initialize rootPath in constructor
        $this->loadEncryptionKey();
        $this->loadSetupKey();
    }

    protected function render(string $template, array $data = [], string $layout = 'default', $templateCategory = null): string
    {
        $templatePath = $this->base_path('templates/' . ($templateCategory ?? $this->getTemplateCategory()) . '/' . $template . '.php');
        $layoutPath = $this->base_path('templates/' . ($templateCategory ?? $this->getTemplateCategory()) . '/layouts/' . $layout . '.php');

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Template not found: {$templatePath}");
        }
        if (!file_exists($layoutPath)) {
            throw new \RuntimeException("Layout not found: {$layoutPath}");
        }

        // Get the current step's data
        $currentStep = $data['currentStep'] ?? null;

        // Add step-specific data
        if ($currentStep) {
            $data['pageTitle'] = t($currentStep['title']) ?: $currentStep['title'];
            $data['pageDescription'] = t($currentStep['description']) ?: $currentStep['description'];
        }

        // Add view instance to data array
        $data['view'] = $this->view;
        $data['stepsConfig'] = [
            'steps' => $this->stepManager->getStepsWithStatus()
        ];

        // Add languages data
        if (!isset($data['languages'])) {
            $data['languages'] = $this->languageManager->getAvailableLanguages();
        }
        if (!isset($data['selectedLanguageId'])) {
            $currentLanguage = $this->languageManager->getCurrentLanguage();
            $data['selectedLanguageId'] = $currentLanguage['id'] ?? 'en';
        }

        return $this->view->render($template, $data, $layout, $templateCategory);
    }

    protected function getTemplateCategory(): string
    {
        $configPath = $this->base_path('configs/general.json');
        $configFileContent = file_get_contents($configPath);
        $config = json_decode($configFileContent, true);

        return $config['default_template'] ?? 'general';
    }


    /**
     * Redirect to a specific route
     * @param string $route The route to redirect to
     * @param array $params Optional parameters
     * @return void
     */
    protected function redirect(string $route, array $params = []): void
    {
        // Block redirects for POST requests
        if ($this->method() === 'POST') {
            throw new \RuntimeException("Cannot redirect POST requests");
        }

        $url = $this->buildUrl($route, $params);
        header('Location: ' . $url);
        exit;
    }

    protected function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Build URL from route and parameters
     * @param string $route The route name
     * @param array $params URL parameters
     * @return string
     */
    private function buildUrl(string $route, array $params = []): string
    {
        $baseUrl = rtrim($_SERVER['SCRIPT_NAME'], 'index.php');
        $url = $baseUrl . $route;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    protected function getRootPath(): string
    {
        return $this->rootPath;
    }

    protected function determineRootPath(): string
    {
        return realpath(__DIR__ . '/../../../../');
    }

    // Function to generate a strong random encryption key
    private function generateEncryptionKey(): string
    {
        return bin2hex(random_bytes(16)); // Generate 32-character random key (16 bytes)
    }

    // Load the encryption key from config or generate and save if not exists
    private function loadEncryptionKey(): void
    {
        $configPath = $this->base_path('configs/general.json');

        // Check if config file exists, create if not
        if (!file_exists($configPath)) {
            file_put_contents($configPath, json_encode([]));
        }

        // Read the config file content
        $configFileContent = file_get_contents($configPath);
        $config = json_decode($configFileContent, true);

        // Check if the encryption key exists in the config
        if (!isset($config['encryption_key']) || empty($config['encryption_key'])) {
            // Generate a new key if not set in config
            $newKey = $this->generateEncryptionKey();

            // Update the config with the new key
            $config['encryption_key'] = $newKey;
            file_put_contents($configPath, json_encode($config));
        }

        // Load the encryption key for use in the controller
        $this->encryptionKey = $config['encryption_key'];
    }

    // Function to encrypt data using the loaded encryption key
    private function encryptData(string $data): string
    {
        $ivlen = openssl_cipher_iv_length('aes-256-cbc');
        $iv = random_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt(
            $data,
            'aes-256-cbc',
            hex2bin($this->encryptionKey),
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        );
        return base64_encode($iv . $ciphertext_raw);
    }

    // Function to decrypt data using the loaded encryption key
    private function decryptData(string $encryptedData): string
    {
        if ($encryptedData === null) {
            throw new \RuntimeException("Encrypted data is null");
        }

        $data = base64_decode($encryptedData);
        $ivlen = openssl_cipher_iv_length('aes-256-cbc');

        // Ensure the data is long enough to contain the IV
        if (strlen($data) < $ivlen) {
            throw new \RuntimeException("Encrypted data is too short to contain the IV");
        }

        $iv = substr($data, 0, $ivlen);
        $ciphertext_raw = substr($data, $ivlen);

        $decryptedData = openssl_decrypt(
            $ciphertext_raw,
            'aes-256-cbc',
            hex2bin($this->encryptionKey),
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        );

        if ($decryptedData === false) {
            throw new \RuntimeException("Decryption failed");
        }

        return $decryptedData;
    }

    // Function to load the setup key from the configs/general.json file
    private function loadSetupKey(): void
    {
        $configPath = $this->base_path('configs/general.json');

        // Read the config file content
        $configFileContent = file_get_contents($configPath);
        $config = json_decode($configFileContent, true);

        // Check if the setup key exists in the config
        if (!isset($config['setup_key']) || empty($config['setup_key'])) {
            // Generate a new key if not set in config
            $newKey = $this->generateEncryptionKey();

            // Encrypt the new key
            $encryptedKey = $this->encryptData($newKey);

            // Update the config with the new keys
            $config['setup_key'] = $encryptedKey;
            $config['original_setup_key'] = $newKey;
            file_put_contents($configPath, json_encode($config));
        }

        // Load the setup key for use in the controller
        $this->setupKey = $config['setup_key'];

        // Check if the original setup key exists in the config
        if (!isset($config['original_setup_key']) || empty($config['original_setup_key'])) {
            // If original_setup_key is missing, generate it by decrypting the existing setup_key
            $originalKey = $this->decryptData($this->setupKey);

            // Update the config with the original key
            $config['original_setup_key'] = $originalKey;
            file_put_contents($configPath, json_encode($config));
        }

        // Load the original setup key for comparison
        $this->originalSetupKey = $config['original_setup_key'];
    }

    // Function to get the setup key
    protected function getSetupKey(): string
    {
        if ($this->setupKey === null) {
            throw new \RuntimeException("Setup key is null");
        }

        return $this->setupKey;
    }

    // Function to validate the provided key against the stored setup key
    protected function isValidKey(string $providedKey): bool
    {
        $this->loadEncryptionKey();
        $this->loadSetupKey();

        if ($this->setupKey === null) {
            throw new \RuntimeException("Setup key is null");
        }

        if ($this->originalSetupKey === null) {
            throw new \RuntimeException("Original setup key is null");
        }

        // Decrypt the provided key
        try {
            $decryptedProvidedKey = $this->decryptData($providedKey);
        } catch (\RuntimeException $e) {
            return false;
        }

        // Use hash_equals for secure comparison
        $isValid = hash_equals($this->originalSetupKey, $decryptedProvidedKey);

        return $isValid;
    }

    // Custom function to determine the base path of the application
    protected function base_path(string $path = ''): string
    {
        // Determine the base path by going up from the current file's directory
        $currentDir = dirname(__FILE__);
        $basePath = realpath($currentDir . '/../..'); // Adjust as needed based on your directory structure

        return rtrim($basePath, '/') . '/' . ltrim($path, '/');
    }
}
