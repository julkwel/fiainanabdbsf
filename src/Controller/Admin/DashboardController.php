<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>
 **/

namespace App\Controller\Admin;

use App\Constant\RoleConstant;
use App\Controller\AbstractBaseController;
use App\Entity\User;
use App\Repository\FiainanaRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DashboardController.
 *
 * @Route("/admin")
 */
class DashboardController extends AbstractBaseController
{
    /**
     * DashboardController constructor.
     *
     * @param EntityManagerInterface       $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ParameterBagInterface        $parameterBag
     */
    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder, ParameterBagInterface $parameterBag)
    {
        parent::__construct($manager, $passwordEncoder);
    }

    /**
     * @Route("/dashboard",name="dashboard")
     *
     * @param UserRepository     $userRepository
     * @param FiainanaRepository $fiainanaRepository
     *
     * @return Response
     * @throws Exception
     */
    public function dashboard(UserRepository $userRepository, FiainanaRepository $fiainanaRepository): Response
    {
        $users = $userRepository->findMembers();
        $fiainana = $fiainanaRepository->findAll();

        return $this->render(
            'admin/dashboard/_dashboard.html.twig',
            ['users' => count($users), 'fiainana' => count($fiainana),]
        );
    }
}
