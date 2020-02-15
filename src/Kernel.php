<?php
declare(strict_types=1);

namespace App;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Loader\PhpFileLoader as RoutingLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class Kernel
{
    /** @var FileLocator */
    private $fileLocator;

    /** @var ContainerBuilder */
    private $containerBuilder;

    /** @var PhpFileLoader */
    private $loader;

    /** @var RoutingLoader */
    private $routingLoader;

    /** @var EventDispatcher */
    private $dispatcher;

    /** @var HttpKernel */
    private $kernel;

    public function __construct()
    {
        $this->fileLocator = new FileLocator();
        $this->containerBuilder = new ContainerBuilder();
        $this->loader = new PhpFileLoader($this->containerBuilder, $this->fileLocator);
        $this->routingLoader = new RoutingLoader($this->fileLocator);
        $this->dispatcher = new EventDispatcher();
        $this->kernel = new HttpKernel(
            $this->dispatcher,
            new ContainerControllerResolver($this->containerBuilder),
            new RequestStack(),
            new ArgumentResolver()
        );
    }

    public function run(): void
    {
        $this->loadServices();
        $this->loadRoutes();

        $request = Request::createFromGlobals();

        try {
            $response = $this->kernel->handle($request);
        } catch (\Throwable $e) {
            dump($e);
            exit;
        }

        $response->send();
        $this->kernel->terminate($request, $response);
    }

    private function loadServices(): void
    {
        $this->loader->load($this->getDirname() . '/config/services.php');
        $this->containerBuilder->compile();
    }

    private function loadRoutes(): void
    {
        $matcher = new UrlMatcher(
            $this->routingLoader->load($this->getDirname() . '/config/routes.php'),
            new RequestContext()
        );

        $this->dispatcher->addSubscriber(new RouterListener($matcher, new RequestStack()));
    }

    /**
     * @return string
     */
    private function getDirname(): string
    {
        return dirname(__DIR__);
    }
}
