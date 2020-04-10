<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpKernel\HttpKernel;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
    ;

    $services->load('App\\', '../src/*')
        ->exclude('../src/{DependencyInjection,Entity,Migrations,Tests,Kernel.php}');

    $services->set('event_dispatcher', EventDispatcher::class);
    $services->set('controller_resolver', ContainerControllerResolver::class);
    $services->set('request_stack', RequestStack::class);
    $services->set('argument_resolver', ArgumentResolver::class);
    $services->set('http_kernel', HttpKernel::class)
        ->args(
            [
                ref('event_dispatcher'),
                ref('controller_resolver'),
                ref('request_stack'),
                ref('argument_resolver'),
            ]
        );
};
