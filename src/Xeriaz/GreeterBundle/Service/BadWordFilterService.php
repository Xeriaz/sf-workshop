<?php

declare(strict_types=1);

namespace App\Xeriaz\GreeterBundle\Service;

class BadWordFilterService
{
    /**
     * @var array
     */
    protected $badWords;

    /**
     * @param array $badWords
     */
    public function __construct(array $badWords)
    {
        $this->badWords = $badWords;
    }

    /**
     * @param string $name
     * @throws \Exception
     */
    public function filter(string $name): void
    {
        $name = \strtolower($name);

        if (\in_array($name, $this->badWords)) {
            throw new \Exception('Offensive words are prohibited');
        };
    }
}
