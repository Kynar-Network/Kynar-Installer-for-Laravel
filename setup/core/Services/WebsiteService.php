<?php

namespace App\Services;

class WebsiteService
{
    private string $rootPath;

    public function __construct()
    {
        $this->rootPath = realpath(__DIR__ . '/../../../../');
    }


    public function ensureAppKey(): void
    {
        if (empty($_ENV['APP_KEY'])) {
            $newAppKey = 'base64:' . base64_encode(random_bytes(32));
            $this->setEnvValue('APP_KEY', $newAppKey);
        }
    }

    public function getWebsiteEnvVars(): array
    {
        return [
            ['key' => 'APP_NAME', 'label' => t('website_app_name_label', 'App Name'), 'description' => t('website_app_name_description', 'The name of your application.')],
            ['key' => 'APP_ENV', 'label' => t('website_app_env_label', 'Environment'), 'description' => t('website_app_env_description', 'The environment of your application.'), 'options' => ['local', 'staging', 'production']],
            ['key' => 'APP_DEBUG', 'label' => t('website_app_debug_label', 'Debug Mode'), 'description' => t('website_app_debug_description', 'Enable or disable debug mode.'), 'options' => ['true', 'false']],
            ['key' => 'APP_URL', 'label' => t('website_app_url_label', 'App URL'), 'description' => t('website_app_url_description', 'The base URL of your application.')],
            ['key' => 'APP_LOCALE', 'label' => t('website_app_locale_label', 'Locale'), 'description' => t('website_app_locale_description', 'The default locale for the application.')],
            ['key' => 'APP_FALLBACK_LOCALE', 'label' => t('website_app_fallback_locale_label', 'Fallback Locale'), 'description' => t('website_app_fallback_locale_description', 'The fallback locale if the primary locale is not found.')],
            ['key' => 'APP_FAKER_LOCALE', 'label' => t('website_app_faker_locale_label', 'Faker Locale'), 'description' => t('website_app_faker_locale_description', 'Locale used by Faker for generating dummy data.')],
            ['key' => 'APP_MAINTENANCE_DRIVER', 'label' => t('website_app_maintenance_driver_label', 'Maintenance Driver'), 'description' => t('website_app_maintenance_driver_description', 'Driver used for maintenance mode.'), 'options' => ['file', 'cache']],
            ['key' => 'PHP_CLI_SERVER_WORKERS', 'label' => t('website_php_cli_server_workers_label', 'PHP CLI Server Workers'), 'description' => t('website_php_cli_server_workers_description', 'Number of workers for PHP CLI server.')],
            ['key' => 'BCRYPT_ROUNDS', 'label' => t('website_bcrypt_rounds_label', 'BCrypt Rounds'), 'description' => t('website_bcrypt_rounds_description', 'Rounds used in BCrypt hashing.')]
        ];
    }

    public function getLogEnvVars(): array
    {
        return [
            ['key' => 'LOG_CHANNEL', 'label' => t('website_log_channel_label', 'Log Channel'), 'description' => t('website_log_channel_description', 'Channel used for logging.'), 'options' => ['stack', 'single', 'daily', 'slack', 'syslog']],
            ['key' => 'LOG_STACK', 'label' => t('website_log_stack_label', 'Log Stack'), 'description' => t('website_log_stack_description', 'Stack used for logging.'), 'options' => ['single', 'daily', 'slack', 'syslog']],
            ['key' => 'LOG_DEPRECATIONS_CHANNEL', 'label' => t('website_log_deprecations_channel_label', 'Deprecation Log Channel'), 'description' => t('website_log_deprecations_channel_description', 'Channel used for deprecation warnings.'), 'options' => ['null', 'stack', 'single', 'daily']],
            ['key' => 'LOG_LEVEL', 'label' => t('website_log_level_label', 'Log Level'), 'description' => t('website_log_level_description', 'Minimum level of log messages to capture.'), 'options' => ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency']]
        ];
    }

    public function getEmailEnvVars(): array
    {
        return [
            ['key' => 'MAIL_MAILER', 'label' => t('website_mail_mailer_label', 'Mail Mailer'), 'description' => t('website_mail_mailer_description', 'Mailer used for sending emails.')],
            ['key' => 'MAIL_HOST', 'label' => t('website_mail_host_label', 'Mail Host'), 'description' => t('website_mail_host_description', 'Host address of the mail server.')],
            ['key' => 'MAIL_PORT', 'label' => t('website_mail_port_label', 'Mail Port'), 'description' => t('website_mail_port_description', 'Port number of the mail server.')],
            ['key' => 'MAIL_USERNAME', 'label' => t('website_mail_username_label', 'Mail Username'), 'description' => t('website_mail_username_description', 'Username for the mail server authentication.')],
            ['key' => 'MAIL_PASSWORD', 'label' => t('website_mail_password_label', 'Mail Password'), 'description' => t('website_mail_password_description', 'Password for the mail server authentication.')],
            ['key' => 'MAIL_FROM_ADDRESS', 'label' => t('website_mail_from_address_label', 'From Address'), 'description' => t('website_mail_from_address_description', 'Default "from" address for emails.')],
            ['key' => 'MAIL_FROM_NAME', 'label' => t('website_mail_from_name_label', 'From Name'), 'description' => t('website_mail_from_name_description', 'Default "from" name for emails.')]
        ];
    }

    public function getMailerOptions(): array
    {
        return [
            'smtp' => t('website_mail_mailer_option_smtp', 'SMTP'),
            'sendmail' => t('website_mail_mailer_option_sendmail', 'Sendmail'),
            'mailgun' => t('website_mail_mailer_option_mailgun', 'Mailgun'),
            'ses' => t('website_mail_mailer_option_ses', 'Amazon SES'),
            'postmark' => t('website_mail_mailer_option_postmark', 'Postmark'),
            'log' => t('website_mail_mailer_option_log', 'Log'),
            'array' => t('website_mail_mailer_option_array', 'Array')
        ];
    }

    public function setEnvValue(string $key, string $value): void
    {
        $envPath = $this->rootPath . DIRECTORY_SEPARATOR . '.env';
        $content = file_get_contents($envPath);

        if (strpos($content, $key) !== false) {
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
        } else {
            $content .= "\n{$key}={$value}";
        }

        file_put_contents($envPath, $content);
    }

    public function getAwsEnvVars(): array
    {
        return [
            ['key' => 'AWS_ACCESS_KEY_ID', 'label' => t('website_aws_key_label', 'AWS Access Key ID'), 'description' => t('website_aws_key_description', 'Your AWS access key ID.')],
            ['key' => 'AWS_SECRET_ACCESS_KEY', 'label' => t('website_aws_secret_label', 'AWS Secret Key'), 'description' => t('website_aws_secret_description', 'Your AWS secret access key.')],
            ['key' => 'AWS_DEFAULT_REGION', 'label' => t('website_aws_region_label', 'AWS Region'), 'description' => t('website_aws_region_description', 'The default AWS region for services.')],
            ['key' => 'AWS_BUCKET', 'label' => t('website_aws_bucket_label', 'AWS Bucket'), 'description' => t('website_aws_bucket_description', 'The S3 bucket name for file storage.')],
            ['key' => 'AWS_USE_PATH_STYLE_ENDPOINT', 'label' => t('website_aws_endpoint_label', 'Use Path Style Endpoint'), 'description' => t('website_aws_endpoint_description', 'Whether to use path-style endpoints.'), 'options' => ['true', 'false']]
        ];
    }

    public function getCacheQueueVars(): array
    {
        return [
            ['key' => 'CACHE_STORE', 'label' => t('website_cache_store_label', 'Cache Store'), 'description' => t('website_cache_store_description', 'The cache store to use.'), 'options' => ['file', 'database', 'redis', 'memcached']],
            ['key' => 'QUEUE_CONNECTION', 'label' => t('website_queue_connection_label', 'Queue Connection'), 'description' => t('website_queue_connection_description', 'The queue connection to use.'), 'options' => ['sync', 'database', 'redis', 'beanstalkd']],
            ['key' => 'REDIS_HOST', 'label' => t('website_redis_host_label', 'Redis Host'), 'description' => t('website_redis_host_description', 'The Redis server host.')],
            ['key' => 'REDIS_PASSWORD', 'label' => t('website_redis_password_label', 'Redis Password'), 'description' => t('website_redis_password_description', 'The Redis server password.')],
            ['key' => 'REDIS_PORT', 'label' => t('website_redis_port_label', 'Redis Port'), 'description' => t('website_redis_port_description', 'The Redis server port.')],
            ['key' => 'MEMCACHED_HOST', 'label' => t('website_memcached_host_label', 'Memcached Host'), 'description' => t('website_memcached_host_description', 'The Memcached server host.')]
        ];
    }
}
