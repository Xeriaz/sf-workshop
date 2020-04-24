<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\GreeterService;
use Symfony\Component\HttpFoundation\Response;

class HomepageController
{
    /** @var GreeterService */
    private $greeter;

    /**
     * @param GreeterService $greeter
     */
    public function __construct(GreeterService $greeter)
    {
        $this->greeter = $greeter;
    }

    /**
     * @param string $name
     * @return array<string, float|string>
     */
    public function index(string $name): array
    {
        return [
            'message' => $this->greeter->greet($name),
            'price' => 100000.04123,
        ];
    }

    /**
     * @return Response
     */
    public function __invoke(): Response
    {
        return new Response('Hello World! This is __invoke()');
    }
}
