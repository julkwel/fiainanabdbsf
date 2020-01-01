<?php

namespace App\Controller;

use App\Entity\Filtre;
use App\Form\FiltreType;
use App\Repository\FiltreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/filtre")
 */
class FiltreController extends AbstractBaseController
{
    /**
     * @Route("/", name="filtre_index", methods={"GET"})
     * @param FiltreRepository $filtreRepository
     *
     * @return Response
     */
    public function index(FiltreRepository $filtreRepository): Response
    {
        return $this->render(
            'admin/filtre/index.html.twig',
            [
                'filtres' => $filtreRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="filtre_new", methods={"GET","POST"})
     * @Route("/{id}/edit", name="filtre_edit", methods={"GET","POST"})
     *
     * @param Request     $request
     * @param Filtre|null $filtre
     *
     * @return Response
     */
    public function new(Request $request, ?Filtre $filtre): Response
    {
        $filtre = $filtre ?: new Filtre();
        $form = $this->createForm(FiltreType::class, $filtre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$filtre->getId()) {
                $this->manager->persist($filtre);
            }
            $this->manager->flush();

            return $this->redirectToRoute('filtre_index');
        }

        return $this->render(
            'admin/filtre/new.html.twig',
            [
                'filtre' => $filtre,
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * @Route("/{id}", name="filtre_delete", methods={"DELETE"})
     * @param Filtre $filtre
     *
     * @return Response
     */
    public function delete(Filtre $filtre): Response
    {

        $this->manager->remove($filtre);
        $this->manager->flush();

        return $this->redirectToRoute('filtre_index');
    }
}
