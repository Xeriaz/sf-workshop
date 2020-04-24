<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\HomepageController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes
        ->add('hello', '/hello/{name}')
        ->controller([HomepageController::class, 'index'])

        ->add('helloWorld', '/')
        ->controller(HomepageController::class)
    ;
};

