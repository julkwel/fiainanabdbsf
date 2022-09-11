<?php

namespace App\Command;

use App\Manager\BibleManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BibleImportCommand extends Command
{
    protected static $defaultName = 'fiainana:bible';
    /**
     * @var BibleManager
     */
    private $bibleManager;

    /**
     * @param BibleManager $bibleManager
     * @param string|null  $name
     */
    public function __construct(BibleManager $bibleManager, string $name = null)
    {
        parent::__construct($name);
        $this->bibleManager = $bibleManager;
    }

    /**
     * config
     */
    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addOption('import', null, InputOption::VALUE_NONE);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if ($input->getOption('import')) {
            $this->bibleManager->importData($io);
        }

        return 0;
    }
}
