<?php

declare(strict_types=1);

namespace App\Xeriaz\GreeterBundle\EventListener;

use Symfony\Contracts\EventDispatcher\Event;

class BadWordListener
{
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
     * @param Event $event
     */
    public function onBadWordAction(Event $event): void
    {
        if (\method_exists($event, 'getName') === false) {
            return;
        }

        $name = \strtolower($event->getName());

        if (\in_array($name, $this->badWords)) {
            throw new \Exception('Offensive words are prohibited');
        };
    }
}
