<?php

use App\Xeriaz\GreeterBundle\Command\GreeterCommand;
use App\Xeriaz\GreeterBundle\EventListener\GreetListener;
use App\Xeriaz\GreeterBundle\Service\BadWordFilterService;
use App\Xeriaz\GreeterBundle\Service\GreeterService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return function (ContainerConfigurator $configurator, ContainerBuilder $container) {
    $services = $configurator->services();

    $services->set('xeriaz.greeter.service', GreeterService::class)
        ->args(
            [
                ref('event_dispatcher'),
                $container->getParameter('xeriaz.greeter.greet_words')
            ]
        );

    $services->set('xeriaz.bad_word_filter', BadWordFilterService::class)
        ->args(
            [
                $container->getParameter('xeriaz.greeter.bad_words')
            ]
        );

    $services->set('xeriaz.greeter.command', GreeterCommand::class)
        ->args(
            [
                ref('xeriaz.greeter.service'),
            ]
        )
        ->tag('console.command');

    $container->register('greeter.bad_word.listener',GreetListener::class)
        ->addArgument(new Reference('xeriaz.bad_word_filter'))
        ->addArgument(new Reference('logger'))
        ->addTag(
            'kernel.event_listener',
            [
                'event' => 'greeter.pre_greet',
                'method' => 'onBadWordAction',
            ]
        );

    $container->register('greeter.greet.listener',GreetListener::class)
        ->addArgument(new Reference('xeriaz.bad_word_filter'))
        ->addArgument(new Reference('logger'))
        ->addTag(
            'kernel.event_listener',
            [
                'event' => 'greeter.greet',
                'method' => 'onGreetAction',
            ]
        );

    $container->register('greeter.post_greet.listener',GreetListener::class)
        ->addArgument(new Reference('xeriaz.bad_word_filter'))
        ->addArgument(new Reference('logger'))
        ->addTag(
            'kernel.event_listener',
            [
                'event' => 'greeter.post_greet',
                'method' => 'onPostGreetAction',
            ]
        );
};
