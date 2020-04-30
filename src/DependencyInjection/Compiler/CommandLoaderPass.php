<?php declare(strict_types = 1);

namespace App\DependencyInjection\Compiler;

use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CommandLoaderPass implements CompilerPassInterface
{
	/**
	 * @inheritDoc
	 */
	public function process(ContainerBuilder $container): void
	{
		$loader = $container->register(
			'console.command_loader',
			ContainerCommandLoader::class
		)->setPublic(true);


		$map = [];
		$commands = $container->findTaggedServiceIds('console.command');
		foreach ($commands as $id => $tags) {
			$definition = $container->findDefinition($id);
			$class = $definition->getClass();

			$commandName = $class::getDefaultName();

			if ($commandName !== null) {
				$map[$commandName] = $id;
			}
		}

		$loader->setArguments([
			new Reference('service_container'),
			$map
		]);
	}
}
