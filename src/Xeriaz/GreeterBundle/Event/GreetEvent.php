<?php

declare(strict_types=1);

namespace App\Xeriaz\GreeterBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class GreetEvent extends Event
{
    public const NAME = 'greeter.greet';

    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return \ucfirst($this->name);
    }
}
