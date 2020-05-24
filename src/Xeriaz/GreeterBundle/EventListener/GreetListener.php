<?php

declare(strict_types=1);

namespace App\Xeriaz\GreeterBundle\EventListener;

use App\Xeriaz\GreeterBundle\Event\GreetEvent;
use App\Xeriaz\GreeterBundle\Event\PostGreetEvent;
use App\Xeriaz\GreeterBundle\Event\PreGreetEvent;
use App\Xeriaz\GreeterBundle\Service\BadWordFilterService;
use Psr\Log\LoggerInterface;

class GreetListener
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var BadWordFilterService
     */
    protected $badWordFilter;

    /**
     * @param BadWordFilterService $badWordFilter
     * @param LoggerInterface $logger
     */
    public function __construct(BadWordFilterService $badWordFilter, LoggerInterface $logger)
    {
        $this->badWordFilter = $badWordFilter;
        $this->logger = $logger;
    }

    /**
     * @param PreGreetEvent $event
     * @throws \Exception
     */
    public function onBadWordAction(PreGreetEvent $event): void
    {
        $this->badWordFilter->filter($event->getName());
    }

    /**
     * @param GreetEvent $event
     */
    public function onGreetAction(GreetEvent $event)
    {
        return $event->getName();
    }

    /**
     * @param PostGreetEvent $event
     */
    public function onPostGreetAction(PostGreetEvent $event)
    {
        $this->logger->debug($event->getMessage());
    }
}
