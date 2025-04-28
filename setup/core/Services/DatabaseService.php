<?php

namespace App\Services;

class DatabaseService
{
    private string $rootPath;

    public function __construct()
    {
        $this->rootPath = realpath(__DIR__ . '/../../../..');
    }

    public function testConnection(string $driver, string $host, string $port, string $database, string $username, string $password): bool
    {
        try {
            switch ($driver) {
                case 'sqlite':
                    $dbPath = "{$this->rootPath}/{$database}";
                    if (!file_exists($dbPath)) {
                        throw new \PDOException("SQLite database file not found: {$dbPath}");
                    }
                    $dsn = "sqlite:{$dbPath}";
                    $pdo = new \PDO($dsn);
                    break;

                case 'mysql':
                case 'mariadb':
                    // First try to connect to the server
                    $dsn = "{$driver}:host={$host};port={$port}";
                    $pdo = new \PDO($dsn, $username, $password);

                    // Then try to select the database
                    $pdo->exec("USE `{$database}`");
                    break;

                case 'pgsql':
                    // PostgreSQL requires database name in initial connection
                    $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
                    $pdo = new \PDO($dsn, $username, $password);
                    break;

                case 'sqlsrv':
                    // SQL Server connection
                    $dsn = "sqlsrv:Server={$host},{$port};Database={$database}";
                    $pdo = new \PDO($dsn, $username, $password);
                    break;

                default:
                    throw new \PDOException("Unsupported database driver: {$driver}");
            }

            // Set error mode after connection
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Test the connection with a simple query
            $stmt = $pdo->query('SELECT 1');
            if (!$stmt) {
                throw new \PDOException("Failed to execute test query");
            }

            return true;

        } catch (\PDOException $e) {
            return false;
        }
    }

    public function normalizeSqliteDbName(string $dbName): string
    {
        if (empty($dbName)) {
            $dbName = 'database/database.sqlite';
        }

        if (!preg_match('/\.sqlite$/', $dbName)) {
            $dbName .= '.sqlite';
        }

        if (strpos($dbName, '/') === false && strpos($dbName, '\\') === false) {
            $dbName = 'database/' . $dbName;
        }

        return $dbName;
    }

    public function ensureSQLiteFileExists(string $dbName): bool
    {
        $fullPath = $this->rootPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $dbName);
        $directory = dirname($fullPath);

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                return false;
            }
        }

        if (!file_exists($fullPath)) {
            if (!touch($fullPath)) {
                return false;
            }
            chmod($fullPath, 0644);
        }

        return true;
    }

    public function setEnvValue(string $key, string $value): bool
    {
        $envFile = $this->rootPath . '/.env';
        $currentContent = file_get_contents($envFile);

        $newValue = str_replace(
            ['"', "'"],
            ['\"', "\'"],
            $value
        );

        $pattern = "/^{$key}=.*/m";
        $replacement = "{$key}=\"{$newValue}\"";

        if (preg_match($pattern, $currentContent)) {
            $newContent = preg_replace($pattern, $replacement, $currentContent);
        } else {
            $newContent = $currentContent . "\n" . $replacement;
        }

        return file_put_contents($envFile, $newContent) !== false;
    }

    public function updateEnvFile(array $values): bool
    {
        $envFile = $this->rootPath . '/.env';
        $content = file_get_contents($envFile);

        foreach ($values as $key => $value) {
            $value = $this->formatEnvValue($value);
            $pattern = "/^{$key}=.*/m";

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$value}", $content);
            } else {
                $content .= PHP_EOL . "{$key}={$value}";
            }
        }

        return file_put_contents($envFile, $content) !== false;
    }

    private function formatEnvValue(string $value): string
    {
        // Add quotes if value contains spaces or special characters
        if (preg_match('/[\s\'"\\\\]/', $value)) {
            return '"' . str_replace('"', '\\"', $value) . '"';
        }
        return $value;
    }
}
