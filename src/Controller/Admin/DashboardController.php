<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>
 **/

namespace App\Controller\Admin;

use App\Controller\AbstractBaseController;
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController.
 *
 * @Route("/admin")
 */
class DashboardController extends AbstractBaseController
{
    /**
     * @Route("/dashboard",name="dashboard")
     */
    public function dashboard()
    {
        $users = $this->manager->getRepository(User::class)->findMembers();

        return $this->render(
            'admin/dashboard/_dashboard.html.twig',
            ['users' => count($users),]
        );
    }
}