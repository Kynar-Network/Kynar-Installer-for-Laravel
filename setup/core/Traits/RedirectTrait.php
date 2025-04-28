<?php

namespace App\Traits;

trait RedirectTrait
{
    protected function redirect(string $route): void
    {
        header("Location: " . generateUrl($route));
        exit();
    }
}
