<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>
 **/

namespace App\Controller\Admin;

use App\Controller\AbstractBaseController;
use App\Entity\Fiainana;
use App\Entity\User;
use App\Repository\FiainanaRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DashboardController.
 *
 * @Route("/admin")
 */
class DashboardController extends AbstractBaseController
{
    /**
     * @Route("/dashboard",name="dashboard")
     *
     * @param UserRepository     $userRepository
     * @param FiainanaRepository $fiainanaRepository
     *
     * @return Response
     */
    public function dashboard(UserRepository $userRepository, FiainanaRepository $fiainanaRepository)
    {
        $users = $userRepository->findMembers();
        $fiainana = $fiainanaRepository->findAll();

        return $this->render(
            'admin/dashboard/_dashboard.html.twig',
            ['users' => count($users), 'fiainana' => count($fiainana),]
        );
    }
}