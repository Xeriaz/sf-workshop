<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomepageController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return new Response('Hello ' . $request->get('name'));
    }

    public function __invoke()
    {
        return new Response('Hello World! This is __invoke()');
    }
}
