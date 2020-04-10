<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>
 **/

namespace App\Controller\Admin;

use App\Controller\AbstractBaseController;
use App\Entity\Tosika;
use App\Form\TosikaType;
use App\Repository\TosikaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class TosikaController.
 *
 * @Route("/admin")
 */
class TosikaController extends AbstractBaseController
{
    /** @var TosikaRepository */
    private $repository;

    /**
     * TosikaController constructor.
     *
     * @param EntityManagerInterface       $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TosikaRepository             $tosikaRepository
     */
    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder, TosikaRepository $tosikaRepository)
    {
        parent::__construct($manager, $passwordEncoder);
        $this->repository = $tosikaRepository;
    }

    /**
     * @Route("/tosika/list", name="liste_tosika")
     */
    public function listTosika()
    {
        return $this->render('admin/tosika/_list.html.twig', ['tosikas' => $this->repository->findAll()]);
    }

    /**
     * @Route("/tosika/manage/{id?}", name="manage_tosika")
     *
     * @param Request     $request
     * @param Tosika|null $tosika
     *
     * @return Response
     */
    public function addTosika(Request $request, Tosika $tosika = null)
    {
        $tosika = $tosika ?? new Tosika();
        $form = $this->createForm(TosikaType::class, $tosika);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($tosika);
            $this->manager->flush();

            return $this->redirectToRoute('liste_tosika');
        }

        return $this->render('admin/tosika/_form.html.twig', ['tosika' => $tosika, 'form' => $form->createView()]);
    }

    /**
     * @param Tosika $tosika
     *
     * @Route("/remove/tosika/{id}",name="remove_tosika")
     *
     * @return RedirectResponse
     */
    public function removeTosika(Tosika $tosika)
    {
        $this->manager->remove($tosika);
        $this->manager->flush();

        return $this->redirectToRoute('liste_tosika');
    }
}
