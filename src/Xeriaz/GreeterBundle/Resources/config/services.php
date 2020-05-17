<?php

use App\Xeriaz\GreeterBundle\Command\GreeterCommand;
use App\Xeriaz\GreeterBundle\EventListener\BadWordListener;
use App\Xeriaz\GreeterBundle\Service\GreeterService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return function (ContainerConfigurator $configurator, ContainerBuilder $container) {
    $services = $configurator->services();

    $services->set('xeriaz.greeter.service', GreeterService::class);

    $services->set('xeriaz.greeter.command', GreeterCommand::class)
        ->args(
            [
                ref('xeriaz.greeter.service'),
                ref('event_dispatcher'),
            ]
        )
        ->tag('console.command');


    $container->register('greeter.bad_word.listener',BadWordListener::class)
        ->addTag(
            'kernel.event_listener',
            [
                'event' => 'greeter.pre_greet',
                'method' => 'onBadWordAction',
            ]
        );
};
