<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Twig\PriceExtension;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;

class ViewListener
{
	private $cacheDir;

	/**
	 * @param string $cacheDir
	 */
	public function __construct(string $cacheDir)
	{
		$this->cacheDir = $cacheDir;
	}

	public function onKernelView(ViewEvent $event): void
    {
        $value = $event->getControllerResult();

		if (is_array($value) === false) {
			return;
		}

        $controller = $event->getRequest()->get('_controller');
        [$className, $action] = $controller;

        $classname = $this->getClassname($className);
        $templatePath = "{$classname}/{$action}.html.twig";

        $response = new Response();

		// Extract Twig Environment initialization to Service Container

		$twigTemplatePath = $this->getProjectDir() . '/templates/'; // TODO: extract into a parameter like kernel.cache_dir
		$loader = new FilesystemLoader($twigTemplatePath); // Twig FileSystem Loader will use the parameter
        $twig = new Environment( // Environment Should Be Also in the Dependency Container
            $loader,
            [
            'cache' => $this->cacheDir,
            ]
        );

        // TODO: make this automatic, based on tags: if Price Extension has a tag "twig.extension" this should happen automattically
        $twig->addExtension(new PriceExtension());

        try {
            $response->setContent(
            	$twig->render($templatePath, $value) // $this should become $this->twig->render
			);
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
