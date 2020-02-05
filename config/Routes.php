<?php

use src\Controller\HomepageController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('hello', '/hello/{name}')
        ->controller(HomepageController::class);
};

