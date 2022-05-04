<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Doctrine\Persistence\ManagerRegistry;

use App\Service\YafileParser;

#[AsCommand(
    name: 'parse',
    description: 'Ya .csv parser',
)]
class ParseCommand extends Command
{
    private ManagerRegistry $doctrine;
    private YafileParser $parser;

    public function __construct(ManagerRegistry $doctrine, YafileParser $parser)
    {
        $this->doctrine = $doctrine;
        $this->parser = $parser;

        parent::__construct();
    }
    protected function configure(): void
    {
        /*
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
        */
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /*
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }
        */

        $print = [];

        $io->success('BEGIN');
        $print = $this->parser->yafiles();

        $io->listing($print);

        $io->success('END ' );

        return Command::SUCCESS;
    }
}
