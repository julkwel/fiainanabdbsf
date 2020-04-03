<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>
 **/

namespace App\Controller\User;

use App\Constant\RoleConstant;
use App\Controller\AbstractBaseController;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 *
 * @Route("/admin/user")
 */
class UserController extends AbstractBaseController
{
    public const PATH_MEMBERS = '/admin/user/members';

    /**
     * @Route("/list/{slug?}", name="list_user")
     *
     * @param UserRepository $userRepository
     * @param string|null    $slug
     *
     * @return Response
     */
    public function list(UserRepository $userRepository, ?string $slug)
    {
        $user = $userRepository->findAdmin();

        if ($slug) {
            $user = $userRepository->findMembers();

            return $this->render(
                'user/_list_membres.html.twig',
                [
                    'users' => $user,
                ]
            );
        }

        return $this->render(
            'user/list_user.html.twig',
            [
                'users' => $user,
            ]
        );
    }

    /**
     * @param Request   $request
     * @param User|null $user
     *
     * @return Response
     *
     * @Route("/manage/{id?}", name="manage_user")
     * @Route("/members/{id?}",name="manage_members")
     */
    public function manage(Request $request, ?User $user)
    {
        $user = $user ? $user : new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pass = $this->encoder->encodePassword($user, $user->getPassword());
            if ($this->getIsMembers($request)) {
                $user->setRoles([RoleConstant::ROLES['User']]);
                $respRoute = $this->redirectToRoute('list_user', ['slug' => 'members']);
            } else {
                $user->setRoles([RoleConstant::ROLES['Admin']]);
                $respRoute = $this->redirectToRoute('list_user');
            }
            $user->setPassword($pass);

            if (!$user->getId()) {
                $this->manager->persist($user);
            }
            $this->manager->flush();

            return $respRoute;
        }

        return $this->render(
            'user/user_form.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function getIsMembers(Request $request)
    {
        return self::PATH_MEMBERS === $request->getPathInfo();
    }

    /**
     * @param User $user
     *
     * @return RedirectResponse
     *
     * @Route("/delete/{id}", name="delete_user")
     */
    public function delete(User $user)
    {
        if ($user !== $this->getUser()) {
            $this->manager->remove($user);
            $this->manager->flush();
        }

        return $this->redirectToRoute('list_user');
    }

}
