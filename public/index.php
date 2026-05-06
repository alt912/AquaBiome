<?php
$_SERVER['HTTPS'] = 'on';
$_SERVER['TRUSTED_PROXIES'] = $_SERVER['TRUSTED_PROXIES'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1, 127.0.0.2';
$_SERVER['TRUSTED_HEADERS'] = 'x-forwarded-for,x-forwarded-host,x-forwarded-proto,x-forwarded-port,x-forwarded-prefix';


use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
