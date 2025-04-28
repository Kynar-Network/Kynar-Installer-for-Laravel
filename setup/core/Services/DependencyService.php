<?php

namespace App\Services;

class DependencyService
{
    protected string $rootPath;
    private array $steps = [
        'composer' => [
            'weight' => 50,
            'progress' => 0,
            'startTime' => 0,
            'estimatedTime' => 120 // 2 minutes estimated
        ],
        'npm' => [
            'weight' => 30,
            'progress' => 0,
            'startTime' => 0,
            'estimatedTime' => 60  // 1 minute estimated
        ],
        'build' => [
            'weight' => 20,
            'progress' => 0,
            'startTime' => 0,
            'estimatedTime' => 30  // 30 seconds estimated
        ]
    ];
    private int $totalProgress = 0;
    private float $startTime;
    private string $currentStep = '';

    public function __construct()
    {
        $this->rootPath = realpath(__DIR__ . '/../../../..');
        $this->startTime = microtime(true);
    }

    public function streamInstallation(): void
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        chdir($this->rootPath);

        try {
            // Composer installation (50%)
            $this->startStep('composer');
            $this->sendMessage(t('starting_composer_installation', 'Starting Composer installation...'));
            $this->runCommand('composer install --no-interaction 2>&1', 'composer');
            $this->updateProgress('composer', 100);

            // NPM installation (30%)
            $this->startStep('npm');
            $this->sendMessage("\n" . t('starting_npm_installation', 'Starting NPM installation...'));
            $this->runCommand('npm install 2>&1', 'npm');
            $this->updateProgress('npm', 100);

            // Build assets (20%)
            $this->startStep('build');
            $this->sendMessage("\n" . t('building_assets', 'Building assets...'));
            $this->runCommand('npm run build 2>&1', 'build');
            $this->updateProgress('build', 100);

            $this->sendMessage("\n" . t('install_dependencies_completed', '✓ All dependencies installed successfully'));
        } catch (\Exception $e) {
            $this->sendMessage("\n" . t('error', '❌ Error: ') . $e->getMessage());
        }
    }

    private function startStep(string $step): void
    {
        if (isset($this->steps[$step])) {
            $this->currentStep = $step;
            $this->steps[$step]['startTime'] = microtime(true);
            $this->sendProgress();
        }
    }

    private function runCommand(string $command, string $step): void
{
    $descriptorspec = [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"]
    ];

    $process = proc_open($command, $descriptorspec, $pipes, $this->rootPath);

    if (!is_resource($process)) {
        throw new \RuntimeException("Failed to execute: $command");
    }

    stream_set_blocking($pipes[1], false);
    stream_set_blocking($pipes[2], false);

    $stepProgress = 0;
    $lastProgressUpdate = microtime(true);
    $progressUpdateInterval = 0.5; // Update every 0.5 seconds

    while (true) {
        $status = proc_get_status($process);

        // Read from pipes
        foreach ([1, 2] as $pipe_id) {
            while ($line = fgets($pipes[$pipe_id])) {
                if (trim($line) !== '') {
                    $this->sendMessage(trim($line));

                    // Update progress less frequently
                    $currentTime = microtime(true);
                    if ($currentTime - $lastProgressUpdate >= $progressUpdateInterval) {
                        $stepProgress = min(100, $stepProgress + 1);
                        $this->updateProgress($step, $stepProgress);
                        $lastProgressUpdate = $currentTime;
                    }
                }
            }
        }

        if (!$status['running']) {
            break;
        }

        usleep(50000); // 50ms delay
    }

    // Ensure 100% progress on completion
    $this->updateProgress($step, 100);

    foreach ($pipes as $pipe) {
        fclose($pipe);
    }

    $exitCode = proc_close($process);
    if ($exitCode !== 0) {
        throw new \RuntimeException("Command failed with exit code: $exitCode");
    }
}

    private function sendMessage(string $message): void
    {
        echo "data: " . json_encode([
            'type' => 'message',
            'message' => $message
        ]) . "\n\n";

        @ob_end_flush();
        flush();
    }

    private function updateProgress(string $step, int $progress): void
{
    if (isset($this->steps[$step])) {
        $this->steps[$step]['progress'] = min(100, $progress);
        $this->calculateTotalProgress();

        $currentTime = microtime(true);
        $elapsedTime = $currentTime - $this->startTime;

        // Calculate remaining time based on progress rate
        $remainingTime = 0;
        if ($this->totalProgress > 0) {
            $progressRate = $this->totalProgress / $elapsedTime; // Progress per second
            $remainingProgress = 100 - $this->totalProgress;
            $remainingTime = $remainingProgress / $progressRate;
        }

        echo "data: " . json_encode([
            'type' => 'progress',
            'progress' => round($this->totalProgress),
            'steps' => $this->steps,
            'time' => [
                'elapsed' => round($elapsedTime),
                'remaining' => max(0, round($remainingTime)),
                'estimated' => $this->steps[$step]['estimatedTime']
            ]
        ]) . "\n\n";

        @ob_end_flush();
        flush();
    }
}

    private function calculateTotalProgress(): void
    {
        $this->totalProgress = 0;
        foreach ($this->steps as $step => $info) {
            $this->totalProgress += ($info['progress'] * $info['weight'] / 100);
        }
    }

    private function sendProgress(): void
    {
        $currentTime = microtime(true);
        $elapsedTime = $currentTime - $this->startTime;

        // Calculate remaining time based on progress and elapsed time
        $remainingTime = 0;
        if ($this->totalProgress > 0) {
            $remainingTime = ($elapsedTime / $this->totalProgress) * (100 - $this->totalProgress);
        }

        echo "data: " . json_encode([
            'type' => 'progress',
            'progress' => round($this->totalProgress),
            'steps' => $this->steps,
            'time' => [
                'elapsed' => round($elapsedTime),
                'remaining' => round($remainingTime),
                'total' => round($elapsedTime + $remainingTime)
            ]
        ]) . "\n\n";

        @ob_end_flush();
        flush();
    }
}
