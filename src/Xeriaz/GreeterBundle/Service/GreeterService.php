<?php

declare(strict_types=1);

namespace App\Xeriaz\GreeterBundle\Service;

use App\Xeriaz\GreeterBundle\Event\PostGreetEvent;
use App\Xeriaz\GreeterBundle\Event\PreGreetEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class GreeterService
{
    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var array
     */
    protected $greetingWords;

    public function __construct(EventDispatcher $dispatcher, array $greetingWords)
    {
        $this->dispatcher = $dispatcher;
        $this->greetingWords = $greetingWords;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function greet(string $name): string
    {
        $this->dispatchPreGreetEvent($name);

        $greetWord = $this->greetingWords[
            \rand(0, count($this->greetingWords) - 1)
        ];

        $message = "{$greetWord} {$name}";

        $this->dispatchPostGreetEvent($message);

        return $message;
    }

    /**
     * @param string $name
     */
    private function dispatchPreGreetEvent(string $name): void
    {
        $event = new PreGreetEvent($name);

        $this->dispatcher->dispatch(
            $event, PreGreetEvent::NAME
        );
    }

    /**
     * @param string $message
     */
    private function dispatchPostGreetEvent(string $message): void
    {
        $event = new PostGreetEvent($message);

        $this->dispatcher->dispatch(
            $event, PostGreetEvent::NAME
        );
    }
}
