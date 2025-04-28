<?php

namespace App\Traits;

trait LaravelBootstrap
{
    protected $app;
    protected $errors = [];

    public function addError(string $error): void
    {
        echo "addError called with: $error\n"; // Debugging statement
        $this->errors[] = $error;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }


    protected function bootLaravel(): void
    {
        try {
            // Add Laravel's autoloader
            $autoloaderPath = __DIR__ . '/../../../../vendor/autoload.php';
            if (!file_exists($autoloaderPath)) {
                throw new \RuntimeException('Composer autoloader not found. Please run "composer install".');
            }
            require_once $autoloaderPath;

            // Initialize Laravel
            $bootstrapPath = __DIR__ . '/../../../../bootstrap/app.php';
            if (!file_exists($bootstrapPath)) {
                throw new \RuntimeException('Laravel bootstrap file not found.');
            }
            $app = require $bootstrapPath;

            // Store application instance
            $this->app = $app;

            // Boot the application
            $kernel = $app->make('Illuminate\Foundation\Http\Kernel');

            // Manually create request without using Request class
            $request = $this->createRequest();

            // Bind request to container
            $app->instance('request', $request);

            // Bootstrap kernel
            $kernel->bootstrap();

            // Initialize database connection
            $app->make('db');

        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to bootstrap Laravel: ' . $e->getMessage());
        }
    }

    protected function createRequest()
    {
        return new \Symfony\Component\HttpFoundation\Request(
            $_GET,
            $_POST,
            [],
            $_COOKIE,
            $_FILES,
            $_SERVER
        );
    }

    protected function getApp()
    {
        if (!$this->app) {
            $this->bootLaravel();
        }
        return $this->app;
    }

    protected function getKernel()
    {
        return $this->getApp()->make('Illuminate\Foundation\Http\Kernel');
    }

    protected function getRequest()
    {
        return $this->app->make('request');
    }
}
