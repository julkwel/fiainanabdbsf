<?php
/**
 * @author <julienrajerison5@gmail.com>
 */

namespace App\Command;

use App\Manager\FiainanaManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class FiainanaCommand.
 */
class FiainanaCommand extends Command
{
    protected static $defaultName = 'fiainana:consume';

    /**
     * @var FiainanaManager
     */
    private $fiainanaManager;

    /**
     * @param FiainanaManager $fiainanaManager
     * @param string|null     $name
     */
    public function __construct(FiainanaManager $fiainanaManager, string $name = null)
    {
        parent::__construct($name);
        $this->fiainanaManager = $fiainanaManager;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->addOption('send-to', null, InputOption::VALUE_REQUIRED)
            ->addOption('all', null, InputOption::VALUE_NONE);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $sendTo = $input->getOption('send-to');
        if ($sendTo) {
            $this->fiainanaManager->sendTo($symfonyStyle, $sendTo);
            exit(0);
        }

        if ($input->getOption('all')) {
            $this->fiainanaManager->sendAll($symfonyStyle);
            exit(0);
        }

        $symfonyStyle->error("Param required !!");
        exit(0);
    }
}
