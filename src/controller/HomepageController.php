<?php
declare(strict_types=1);

//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
namespace src\Controller;

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
}
