<?php
declare(strict_types=1);

namespace App\Xeriaz\GreeterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('xeriaz_greeter');

        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('enable')->defaultFalse()->end()
                ->booleanNode('useBadWordsHelper')->defaultTrue()->end()
                ->booleanNode('useGreetWordsHelper')->defaultTrue()->end()
                ->arrayNode('badWords')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('greetWords')
                    ->scalarPrototype()->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
