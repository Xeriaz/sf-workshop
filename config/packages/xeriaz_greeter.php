<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator, ContainerBuilder $container) {
    $container->loadFromExtension(
        'greeter',
        [
            'enable' => true,
            'badWords' => [
                'poop',
                'poo',
                'geez',
                'darn',
            ],
            'greetWords' => [
                'Konichiwa',
                'Sveiki',
            ],
        ]
    );
};
