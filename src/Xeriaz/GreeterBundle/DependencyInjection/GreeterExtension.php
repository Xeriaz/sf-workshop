<?php

declare(strict_types=1);

namespace App\Xeriaz\GreeterBundle\DependencyInjection;

use App\Xeriaz\GreeterBundle\Resources\helper\BadWordsList;
use App\Xeriaz\GreeterBundle\Resources\helper\GreetWordsList;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class GreeterExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if ($config['enable'] === false) {
            return;
        };

        $container->setParameter('xeriaz.greeter.bad_words', $this->getBadWords($config));
        $container->setParameter('xeriaz.greeter.greet_words', $this->getGreetWords($config));

        $loader = new PhpFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.php');
    }

    /**
     * @param array $config
     * @return array
     */
    private function getBadWords(array $config): array
    {
        if ($config['useBadWordsHelper'] === true) {
            $badWords = array_unique(
                \array_merge(
                    $config['badWords'],
                    (new BadWordsList())->getBadWords()
                )
            );
        }

        return $badWords ?? $config['badWords'];
    }

    private function getGreetWords(array $config)
    {
        if ($config['useGreetWordsHelper'] === true) {
            $greetWords = array_unique(
                \array_merge(
                    $config['greetWords'],
                    (new GreetWordsList())->getGreetWords()
                )
            );
        }

        return $greetWords ?? $config['greetWords'];
    }
}
