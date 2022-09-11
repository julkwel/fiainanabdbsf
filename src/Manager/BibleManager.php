<?php
/**
 * @author <julienrajerison5@gmail.com>
 */

namespace App\Manager;

use App\Entity\Bible;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class BibleManager.
 */
class BibleManager
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param ParameterBagInterface  $parameterBag
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ParameterBagInterface $parameterBag, EntityManagerInterface $entityManager)
    {
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
    }

    /**
     * @param SymfonyStyle $symfonyStyle
     */
    public function importData(SymfonyStyle $symfonyStyle)
    {
        $filePath = fopen($this->parameterBag->get('bible_cleaned_csv'), 'r');
        $progressBar = $symfonyStyle->createProgressBar(31100);
        while (false !== $line = fgetcsv($filePath, 10000)) {
            $bible = new Bible();
            $bible->setLabel($line[0])->setChapter($line[1])->setVerse($line[2])->setContent($line[3]);
            $this->entityManager->persist($bible);
            $this->entityManager->flush();
            $progressBar->advance();
        }

        $progressBar->finish();
        $symfonyStyle->note(sprintf("Done %s", (new DateTime())->format("d-m-Y H:s")));
    }
}
