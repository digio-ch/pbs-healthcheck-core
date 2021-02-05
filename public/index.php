<?php

use App\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/config/bootstrap.php';

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

/**
 * This is needed due to the caddy docker container forwarding.
 * If we define a multiple labels in the Caddyfile to split
 * the webserver from the php container like so
 * :80/api { ... }
 * :80 { ... }
 * Caddy will forward our requests with /api correctly but
 * due to the symfony request factory and PHP globals the $pathInfo
 * will be missing /api and therefore none of our routes will match and a 404 will be thrown
 * the cause of this seems to be $_SERVER['SCRIPT_NAME'] and $_SERVER['PHP_SELF']
 * they have the prefix /api causing bad construction of the pathInfo in the Symfony request object.
 * By removing /api from those globals we can get symfony to behave normally without caring about
 * the proxying and forwarding in the Caddy web server.
 */
$_SERVER['SCRIPT_NAME'] = str_replace('/api', '', $_SERVER['SCRIPT_NAME']);
$_SERVER['PHP_SELF'] = str_replace('/api', '', $_SERVER['PHP_SELF']);

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
