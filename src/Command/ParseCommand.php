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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $print = [];
        $io = new SymfonyStyle($input, $output);

        $io->success('BEGIN');

        $print = $this->parser->yafiles();
        $io->listing($print);

        $io->success('END ' );

        return Command::SUCCESS;
    }
}
