<?php

use App\Languages\LanguageManager;

if (!function_exists('t')) {
    function t(string $key, ?string $default = null, array $params = []): string
    {
        $translation = LanguageManager::getInstance()->translate($key) ?? $default ?? $key;

        if (!empty($params)) {
            foreach ($params as $param => $value) {
                $translation = str_replace(':' . $param, $value, $translation);
            }
        }

        return $translation;
    }
}
