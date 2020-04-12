<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\PhpFileLoader as RoutingLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class Kernel extends BaseKernel
{
    /** @var FileLocator */
    private $fileLocator;

    /** @var int */
    protected $requestStackSize = 0;

    /** @var bool */
    protected $resetServices = false;

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);
        $this->fileLocator = new FileLocator();
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

    /**
     * {@inheritDoc}
     */
    public function handle(Request $request, int $type = HttpKernelInterface::MASTER_REQUEST, bool $catch = true)
    {
        $this->boot();

        $this->loadRoutes($this->container->get('event_dispatcher'));

        ++$this->requestStackSize;
        $this->resetServices = true;

        try {
            return $this->getHttpKernel()->handle($request, $type, $catch);
        } finally {
            --$this->requestStackSize;
        }
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
    public function registerContainerConfiguration(LoaderInterface $loader): void
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

    private function loadRoutes(EventDispatcher $dispatcher): void
    {
        $matcher = new UrlMatcher(
            (new RoutingLoader($this->fileLocator))->load($this->getProjectDir() . '/config/routes.php'),
            new RequestContext()
        );

        $dispatcher->addSubscriber(new RouterListener($matcher, new RequestStack()));
    }
}
