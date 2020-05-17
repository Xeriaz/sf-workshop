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
     * @param Event $event
     */
    public function onBadWordAction(Event $event): void
    {
        if (!($event instanceof PreGreetEvent)
            && \method_exists($event, 'getName') === false
        ) {
            return;
        }

        $name = \strtolower($event->getName());

        if (\in_array($name, $this->badWords)) {
            throw new \Exception('Offensive words are prohibited');
        };
    }

    /**
     * @param Event $event
     */
    public function onGreetAction(Event $event)
    {
        if ($event instanceof PostGreetEvent
            && \method_exists($event, 'getMessage')
        ) {
            $this->logger->debug($event->getMessage());
        }
    }
}
