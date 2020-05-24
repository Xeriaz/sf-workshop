<?php

declare(strict_types=1);

namespace App\Controller;

use App\Xeriaz\GreeterBundle\Service\GreeterService;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomepageController
{
    /**
     * @var FormFactory
     */
    protected $formFactory;

    /** @var GreeterService */
    private $greeter;

    /**
     * @param GreeterService $greeter
     * @param FormFactory $formFactory
     */
    public function __construct(GreeterService $greeter, FormFactory $formFactory)
    {
        $this->greeter = $greeter;
        $this->formFactory = $formFactory;
    }

    /**
     * @param string $name
     * @return array<string, float|string>
     */
    public function index(string $name): array
    {
        $form = $this->formFactory->createBuilder()
            ->add('task', TextType::class)
//            ->add('price', CurrencyType::class)
            ->getForm();

        $request = Request::createFromGlobals();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            \dd($data);

            return [
                'message' => '',
            ];
        }

        return [
            'message' => $this->greeter->greet($name),
            'price' => 100000.04123,
            'form' => $form->createView(),
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
