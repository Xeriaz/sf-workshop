<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Command\GreeterCommand;
use App\EventListener\ViewListener;
use App\Service\GreeterService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormRegistry;
use Symfony\Component\Form\ResolvedFormTypeFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\Log\Logger;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return function (ContainerConfigurator $configurator, ContainerBuilder $container, PhpFileLoader $loader) {
    $services = $configurator->services()
        ->defaults()
        ->bind('$greeter', ref('xeriaz.greeter.service'))
        ->bind('$formFactory', ref('form_factory'))
        ->autowire()
        ->autoconfigure();

    $container->setParameter(
        'twig.template_dir',
        $container->getParameter('kernel.project_dir') . '/templates/'
    );

    $services->instanceof(ExtensionInterface::class)
        ->tag('twig.extension');

    $services->instanceof(Command::class)
        ->tag('console.command');

    $services->load('App\\', '../src/*')
        ->exclude('../src/{DependencyInjection,Entity,Migrations,Tests,Kernel.php,Xeriaz}');

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
    $services->set('twig.filesystem_loader', FilesystemLoader::class)
        ->args(
            [
                $container->getParameter('twig.template_dir'),
            ]
        );
    $services->set('twig.environment', Environment::class)
        ->args(
            [
                ref('twig.filesystem_loader'),
//                ['cache' => $container->getParameter('kernel.cache_dir')], // Avoid twig caching
            ]
        );
    $services->set('greeter.command', GreeterCommand::class)
        ->args([
            ref(GreeterService::class)
        ]);
    $services->set('logger', Logger::class);

    // FORM
    $services->set('http_foundation.extension', HttpFoundationExtension::class);
    $services->set('resolved_form_type.factory', ResolvedFormTypeFactory::class);
    $services->set('form_registry', FormRegistry::class)
        ->args(
            [
                [
                    ref('http_foundation.extension'),
                ],
                ref('resolved_form_type.factory'),
            ]
        );
    $services->set('form_factory', FormFactory::class)
        ->args(
            [
                ref('form_registry'),
            ]
        );

    $container->addCompilerPass(new RegisterListenersPass());
    $container->register(ViewListener::class)
        ->addArgument(
            new Reference('twig.environment')
        )
        ->addArgument(
            $container->getParameter('kernel.cache_dir')
        )
        ->addArgument(
            $container->getParameter('twig.template_dir')
        )
        ->addTag('kernel.event_listener', ['event' => 'kernel.view']);

    $loader->import('./packages/*');
};
