<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>
 **/

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AbstractBaseController.
 */
class AbstractBaseController extends AbstractController
{
    protected $manager;
    protected $encoder;

    /**
     * AbstractBaseController constructor.
     *
     * @param EntityManagerInterface       $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $manager,UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->manager = $manager;
        $this->encoder = $passwordEncoder;
    }

    /**
     * @param $_data
     *
     * @return Response
     */
    public function response($_data)
    {
        $list = new Response($_data);
        $list->headers->set('Content-Type', 'application/json');
        $list->headers->set('Access-Control-Allow-Origin', '*');

        return $list;
    }

    /**
     * @param UploadedFile|null $photo
     *
     * @return string|boolean
     */
    public function upload(?UploadedFile $photo)
    {
        if ($photo instanceof UploadedFile) {
            $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $photo->move($this->getParameter('publication_photo'), $newFilename);
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            return $newFilename;
        }

        return false;
    }
}
