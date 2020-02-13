<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernel;

$request = Request::createFromGlobals();
$kernel = new HttpKernel($dispatcher, $controllerResolver, new RequestStack(), $argumentResolver);

try {
    $response = $kernel->handle($request);
} catch (Throwable $e) {
    dump($e);
    exit;
}

$response->send();
$kernel->terminate($request, $response);
