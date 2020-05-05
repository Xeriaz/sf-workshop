<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\GreeterService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GreeterCommand extends Command
{
	protected static $defaultName = 'app:greet';

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
            $this->greeter->greet(
                $input->getArgument('name')
            )
        );

        return 0;
    }
}
