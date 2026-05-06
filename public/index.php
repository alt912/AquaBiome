<?php
$_SERVER['HTTPS'] = 'on';
if (isset($_SERVER['REMOTE_ADDR'])) {
    $_SERVER['TRUSTED_PROXIES'] = $_SERVER['REMOTE_ADDR'];
}


use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
