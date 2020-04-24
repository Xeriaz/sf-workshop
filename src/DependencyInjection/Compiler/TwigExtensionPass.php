<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TwigExtensionPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('twig.environment')) {
            return;
        }

        $twigEnvironment = $container->findDefinition('twig.environment');
        $taggedServices = $container->findTaggedServiceIds('twig.extension');

        foreach ($taggedServices as $id => $tags) {
            $twigEnvironment->addMethodCall(
                'addExtension',
                [new Reference($id)]
            );
        }
    }
}
