<?php
require __DIR__.'/../vendor/autoload.php';
use Dotenv\Dotenv;
use Dotenv\Util\Str;

function env($key, $default = null)
{
    $dotenv = Dotenv::createUnsafeImmutable( __DIR__."/../");
    $dotenv->load();
    $value = getenv($key);

    if ($value === false) {
        return value($default);
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return;
    }

    if (strlen($value) > 1 && Str::startsWith($value, '"') && Str::endsWith($value, '"')) {
        return substr($value, 1, -1);
    }

    return $value;
}