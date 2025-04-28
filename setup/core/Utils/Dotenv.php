<?php

namespace App\Utils;

class Dotenv
{
    private string $path;
    private array $env = [];

    private function __construct(string $path)
    {
        $this->path = rtrim($path, '/\\') . DIRECTORY_SEPARATOR . '.env';
        $this->load();
    }

    public static function createImmutable(string $path): self
    {
        return new self($path);
    }

    public function load(): void
    {
        if (!file_exists($this->path)) {
            throw new \RuntimeException(".env file not found at: {$this->path}");
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) continue;

            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) continue;

            $key = trim($parts[0]);
            $value = $this->processValue(trim($parts[1]));

            $this->env[$key] = $value;
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }

    public function safeLoad(): void
    {
        try {
            $this->load();
        } catch (\Exception $e) {
            // Silently fail on safe load
        }
    }

    private function processValue(string $value): string
    {
        // Remove quotes
        if (strlen($value) > 1) {
            $first = substr($value, 0, 1);
            $last = substr($value, -1);
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        // Replace environment variables
        return preg_replace_callback('/\${([^}]+)}/', function($matches) {
            return $_ENV[$matches[1]] ?? $matches[0];
        }, $value);
    }

    public function required(array $variables): void
    {
        foreach ($variables as $variable) {
            if (!isset($this->env[$variable])) {
                throw new \RuntimeException("Required environment variable '{$variable}' is not set.");
            }
        }
    }
}
