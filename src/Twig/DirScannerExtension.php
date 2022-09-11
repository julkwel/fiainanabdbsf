<?php
/**
 * @author <julienrajerison5@gmail.com>
 */

namespace App\Twig;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function Clue\StreamFilter\fun;

class DirScannerExtension extends AbstractExtension
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_slides', [$this, 'getSlideFiles']),
        ];
    }

    /**
     * @return array
     */
    public function getSlideFiles()
    {
        $dirs = scandir($this->parameterBag->get('kernel.project_dir').'/public/uploads/slides/');

        return array_filter($dirs, function ($item) {
            return strpos($item, 'jpg') !== false;
        });
    }
}
