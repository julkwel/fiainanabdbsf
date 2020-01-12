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
     * @throws \Exception
     */
    public function dashboard(UserRepository $userRepository, FiainanaRepository $fiainanaRepository)
    {
        $path = $this->getParameter('kernel.project_dir').'/public/upload/contact.yaml';
        $data = Yaml::parseFile($path);
        foreach ($data as $key => $val) {
            $user = $this->manager->getRepository(User::class)->findOneBy(['username' => $key]);
            if (!$user){
                $user = new User();
                $user->setRoles($user->getRoles() ?: [RoleConstant::ROLES['User']]);
                $user->setUsername($key);
                $user->setNom($key);
                $user->setPrenom($val);
                $user->setIsAbone(true);
                $user->setBirthDate(new DateTime('now'));

                if (!$user->getId()) {
                    $user->setPassword($this->encoder->encodePassword($user, '123456'));
                    $this->manager->persist($user);
                }
                $this->manager->flush();
            }
        }
        $users = $userRepository->findMembers();
        $fiainana = $fiainanaRepository->findAll();

        return $this->render(
            'admin/dashboard/_dashboard.html.twig',
            ['users' => count($users), 'fiainana' => count($fiainana),]
        );
    }
}