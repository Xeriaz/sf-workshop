<?php
declare(strict_types=1);

namespace App;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\PhpFileLoader as RoutingLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

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

    /** @var HttpKernel */
//    private $kernel;

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);

        $this->fileLocator = new FileLocator();
        $this->routingLoader = new RoutingLoader($this->fileLocator);
//        $this->dispatcher = new EventDispatcher();
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
        $container->register('debug.event_dispatcher', EventDispatcher::class);

        $matcher = new UrlMatcher(
            (new RoutingLoader($this->fileLocator))->load($this->getProjectDir() . '/config/routes.php'),
            new RequestContext()
        );
        $container->get('debug.event_dispatcher')->addSubscriber(new RouterListener($matcher, new RequestStack()));
    }

    private function loadRoutes(EventDispatcher $dispatcher): void
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
