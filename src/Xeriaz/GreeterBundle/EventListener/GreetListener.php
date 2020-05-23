<?php

declare(strict_types=1);

namespace App\Xeriaz\GreeterBundle\EventListener;

use App\Xeriaz\GreeterBundle\Event\PostGreetEvent;
use App\Xeriaz\GreeterBundle\Event\PreGreetEvent;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\Event;

class GreetListener
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    private $badWords = [
        'poo',
        'poop',
        'geez',
        'darn',
    ];

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param PreGreetEvent $event
     * @throws \Exception
     */
    public function onBadWordAction(PreGreetEvent $event): void
    {
        $name = \strtolower($event->getName());

        if (\in_array($name, $this->badWords)) {
            throw new \Exception('Offensive words are prohibited');
        };
    }

    /**
     * @param PostGreetEvent $event
     */
    public function onGreetAction(PostGreetEvent $event)
    {
        $this->logger->debug($event->getMessage());
    }
}
