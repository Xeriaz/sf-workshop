<?php

declare(strict_types=1);

namespace App\Xeriaz\GreeterBundle\Command;

use App\Xeriaz\GreeterBundle\Event\PostGreetEvent;
use App\Xeriaz\GreeterBundle\Event\PreGreetEvent;
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
     * @param GreeterService $greeter
     */
    public function __construct(GreeterService $greeter)
    {
        parent::__construct(null);
        $this->greeter = $greeter;
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
        $output->writeln(
            $message = $this->greeter->greet(
                $input->getArgument('name')
            )
        );

        return 0;
    }
}
