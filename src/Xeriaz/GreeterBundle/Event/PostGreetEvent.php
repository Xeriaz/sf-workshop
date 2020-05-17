<?php

declare(strict_types=1);

namespace App\Xeriaz\GreeterBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class PostGreetEvent extends Event
{
    public const NAME = 'greeter.post_greet';

    /**
     * @var string
     */
    protected $message;

    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
