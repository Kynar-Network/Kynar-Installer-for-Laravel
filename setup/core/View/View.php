<?php

namespace App\View;

use App\Languages\LanguageManager;

class View
{
    private $layout = 'default';
    private $viewPath;
    private $sections = [];
    private ?string $currentSection = null;
    private $data = [];
    public ?LanguageManager $languageManager = null;

    public function __construct()
    {
        $this->viewPath = realpath(__DIR__ . '/../../templates');
        $this->languageManager = new LanguageManager();
    }

    public function render($template, array $data = [], string $layout = 'default', $templateCategory = null): string
    {
        // Debug information
        error_log('Rendering template: ' . $template);
        error_log('Template data: ' . json_encode($data));

        // Store data for includes and sections
        $this->data = $data;

        // Get the template category from config if not provided
        if ($templateCategory === null) {
            $configPath = realpath(__DIR__ . '/../../configs/general.json');
            if (!file_exists($configPath)) {
                throw new \RuntimeException("Config file not found: {$configPath}");
            }
            $config = json_decode(file_get_contents($configPath), true);
            $templateCategory = $config['default_template'] ?? 'general';
        }

        // Get template content
        $templatePath = $this->viewPath . DIRECTORY_SEPARATOR . $templateCategory . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $template) . '.php';
        error_log('Template path: ' . $templatePath);

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Template not found: {$templatePath}");
        }

        try {
            // Make data available to template
            extract($this->data, EXTR_SKIP);

            // Start output buffering
            ob_start();
            include $templatePath;
            $content = ob_get_clean();

            // If layout is set, wrap content in layout
            if ($layout) {
                $layoutPath = $this->viewPath . DIRECTORY_SEPARATOR . $templateCategory . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $layout . '.php';
                error_log('Layout path: ' . $layoutPath);

                if (!file_exists($layoutPath)) {
                    throw new \RuntimeException("Layout not found: {$layoutPath}");
                }

                // Make data available to layout and include the content
                $mainContent = $content;
                ob_start();
                include $layoutPath;
                return ob_get_clean();
            }

            return $content;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw new \RuntimeException("Error rendering template: " . $e->getMessage(), 0, $e);
        }
    }


    public function includePath($path, array $additionalData = []): void
    {
        // Normalize path separators
        $normalizedPath = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $fullPath = $this->viewPath . DIRECTORY_SEPARATOR . $normalizedPath . '.php';

        if (!file_exists($fullPath)) {
            throw new \RuntimeException("Template file not found: {$fullPath}");
        }

        // Merge additional data with stored data
        $mergedData = array_merge($this->data, $additionalData);

        // Extract all variables for included template
        extract($mergedData);

        include $fullPath;
    }

    public function include(string $template, array $data = []): void
    {
        // Merge current data with passed data (passed data takes precedence)
        $mergedData = array_merge($this->data, $data);
        extract($mergedData, EXTR_SKIP);

        $templatePath = $this->viewPath . DIRECTORY_SEPARATOR . $template . '.php';
        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Included template not found: {$templatePath}");
        }

        include $templatePath;
    }

    public function section(string $name): void
    {
        $this->currentSection = $name;
        ob_start();
    }

    public function endSection(): void
    {
        if ($this->currentSection === null) {
            throw new \RuntimeException('No section started');
        }

        $this->sections[$this->currentSection] = ob_get_clean();
        $this->currentSection = null;
    }

    public function yield(string $name): void
    {
        echo $this->sections[$name] ?? '';
    }

    public function hasSection(string $name): bool
    {
        return isset($this->sections[$name]);
    }

    public function getSection($name): string
    {
        return $this->sections[$name] ?? '';
    }
}
