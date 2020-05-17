<?php

use App\Xeriaz\GreeterBundle\Command\GreeterCommand;
use App\Xeriaz\GreeterBundle\EventListener\GreetListener;
use App\Xeriaz\GreeterBundle\Service\GreeterService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Log\Logger;
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

    $container->register('greeter.bad_word.listener',GreetListener::class)
        ->addArgument(new Reference('logger'))
        ->addTag(
            'kernel.event_listener',
            [
                'event' => 'greeter.pre_greet',
                'method' => 'onBadWordAction',
            ]
        );

    $container->register('greeter.post_greet.listener',GreetListener::class)
        ->addArgument(new Reference('logger'))
        ->addTag(
            'kernel.event_listener',
            [
                'event' => 'greeter.post_greet',
                'method' => 'onGreetAction',
            ]
        );
};
