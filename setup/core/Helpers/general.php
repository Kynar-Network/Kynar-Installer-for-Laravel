<?php

if (!function_exists('getParentFolder')) {
    function getParentFolder($path, $levelsUp)
    {
        for ($i = 0; $i < $levelsUp; $i++) {
            $path = dirname($path);
        }
        return $path;
    }
}
