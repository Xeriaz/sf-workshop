<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;

class ViewListener
{
    public function onKernelView(ViewEvent $event): void
    {
        $value = $event->getControllerResult();
        $classname = $this->getClassname($value['controller']);
        $templatePath = "{$classname}/{$classname}.html.twig";

        $response = new Response();
        $loader = new FilesystemLoader($this->getProjectDir() . '/templates/');

        $twig = new Environment(
            $loader,
            [
//            'cache' => $this->getProjectDir() . '/var/twig/compilation_cache',
            ]
        );

        try {
            $response->setContent($twig->render($templatePath, $value));
        } catch (LoaderError $exception) {
            try {
                $response->setContent($twig->render($this->getFallbackTemplate($classname), $value));
            } catch (LoaderError $exception) {
                $response->setContent($twig->render($this->getErrorTemplate(), $value));
            }
        }

        $event->setResponse($response);
    }

    /**
     * @return string
     */
    private function getProjectDir(): string
    {
        return \dirname(\dirname(__DIR__));
    }

    /**
     * @param string $controller
     * @return string
     */
    private function getClassname(string $controller): string
    {
        return \strtolower(
            substr(
                end(\explode('\\', $controller)),
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
     * @param string $classname
     * @return string
     */
    private function getFallbackTemplate(string $classname): string
    {
        return "{$classname}/index.html.twig";
    }
}
