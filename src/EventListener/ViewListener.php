<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Twig\PriceExtension;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ViewListener
{
    /** @var Environment */
    protected $twigEnv;

    /** @var string */
    protected $twigDir;

    /** @var string */
    private $cacheDir;

    /**
     * @param Environment $twigEnv
     * @param string $cacheDir
     * @param string $twigDir
     */
    public function __construct(Environment $twigEnv, string $cacheDir, string $twigDir)
    {
        $this->twigEnv = $twigEnv;
        $this->cacheDir = $cacheDir;
        $this->twigDir = $twigDir;
    }

    /**
     * @param ViewEvent $event
     * @throws LoaderError
     * @throws  RuntimeError
     * @throws SyntaxError
     */
    public function onKernelView(ViewEvent $event): void
    {
        $value = $event->getControllerResult();

        if (is_array($value) === false) {
            return;
        }

        $controller = $event->getRequest()->get('_controller');
        [$className, $action] = $controller;

        $classname = $this->getClassName($className);
        $templatePath = "{$classname}/{$action}.html.twig";

        $response = new Response();

        // TODO: make this automatic, based on tags: if Price Extension has a tag "twig.extension"
        // this should happen automatically
        $this->twigEnv->addExtension(new PriceExtension());

        $response->setContent(
            $this->getRenderedTwig($templatePath, $value)
        );

        $event->setResponse($response);
    }

    /**
     * @param string $controller
     * @return string
     */
    private function getClassName(string $controller): string
    {
        $pathArray = \explode('\\', $controller);

        return \strtolower(
            substr(
                (string)end($pathArray),
                0,
                -10
            )
        );
    }

    /**
     * @return string
     */
    private function getErrorTemplate(): string
    {
        return "error/error500.html.twig";
    }

    /**
     * @param string $templatePath
     * @param array $value
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function getRenderedTwig(string $templatePath, array $value): string
    {
        try {
            $renderedTwig = $this->twigEnv->render($templatePath, $value);
        } catch (LoaderError $exception) {
            $renderedTwig = $this->twigEnv->render($this->getErrorTemplate(), $value);
        }

        return $renderedTwig;
    }
}
