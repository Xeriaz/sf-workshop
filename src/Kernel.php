<?php
declare(strict_types=1);

namespace App;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\PhpFileLoader as RoutingLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Kernel extends BaseKernel
{
    /** @var FileLocator */
    private $fileLocator;

    /** @var PhpFileLoader */
    private $loader;

    /** @var RoutingLoader */
    private $routingLoader;

    /** @var EventDispatcher */
    private $dispatcher;

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);

        $this->fileLocator = new FileLocator();
        $this->routingLoader = new RoutingLoader($this->fileLocator);
    }

    public function run(): void
    {
        $request = Request::createFromGlobals();

        try {
            $response = $this->handle($request);
        } catch (\Throwable $e) {
            dump($e);
            exit;
        }

        $response->send();
        $this->terminate($request, $response);
    }

    protected function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterListenersPass());

		$container->register('event_dispatcher', EventDispatcher::class)
			->setPublic(true);

		$container->register('controller_resolver', ContainerControllerResolver::class)
			->setArguments(
				[
					new Reference('service_container'),
				]
			);
		$container->register('request_stack', RequestStack::class);
		$container->register('argument_resolver', ArgumentResolver::class);

		$container->register('http_kernel', HttpKernel::class)
			->setPublic(true)
			->setArguments(
				[
					new Reference('event_dispatcher'),
					new Reference('controller_resolver'),
					new Reference('request_stack'),
					new Reference('argument_resolver'),
				]
			);

		// TODO: configure the event dispatcher.

		/** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $container->get('event_dispatcher');
		$this->loadRoutes($dispatcher);

//		$container->set('event_dispatcher', $dispatcher);
    }

    private function loadRoutes(EventDispatcherInterface $dispatcher): void
    {
        $matcher = new UrlMatcher(
            (new RoutingLoader($this->fileLocator))->load($this->getProjectDir() . '/config/routes.php'),
            new RequestContext()
        );

        $dispatcher->addSubscriber(new RouterListener($matcher, new RequestStack()));
    }

    /**
     * @inheritDoc
     */
    public function registerBundles()
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getProjectDir() . '/config/services.php');
    }

    /**
     * @return string
     */
    public function getProjectDir(): string
    {
        return dirname(__DIR__);
    }
}
