<?php

declare(strict_types=1);

namespace App\Xeriaz\GreeterBundle\Command;

use App\Xeriaz\GreeterBundle\Event\GreetEvent;
use App\Xeriaz\GreeterBundle\Service\GreeterService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class GreeterCommand extends Command
{
	protected static $defaultName = 'xeriaz:greet';

    /**
     * @var GreeterService
     */
    protected $greeter;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @param GreeterService $greeter
     */
    public function __construct(GreeterService $greeter, EventDispatcher $dispatcher)
    {
        parent::__construct(null);
        $this->greeter = $greeter;
        $this->dispatcher = $dispatcher;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Greet user')
            ->addArgument('name', InputArgument::REQUIRED, 'Name to greet')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $event = new GreetEvent($input->getArgument('name'));
        $this->dispatcher->dispatch(
            $event, GreetEvent::NAME
        );

        $output->writeln(
            $this->greeter->greet(
                $input->getArgument('name')
            )
        );

        return 0;
    }
}
