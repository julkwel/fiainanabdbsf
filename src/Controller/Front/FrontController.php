<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>
 **/

namespace App\Controller\Front;

use App\Constant\RoleConstant;
use App\Controller\AbstractBaseController;
use App\Entity\Fiainana;
use App\Entity\Filtre;
use App\Entity\User;
use App\Form\UserType;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FrontController.
 */
class FrontController extends AbstractBaseController
{
    const SUBJECT = 'Email avy amin\'ny mpanaraka ny fiainana be dia be';

    /**
     * @Route("/",name="home_page")
     *
     * @return Response
     */
    public function home()
    {
        $filtres = $this->manager->getRepository(Filtre::class)->findAll();
        $fiainana = $this->manager->getRepository(Fiainana::class)->findAll();

        return $this->render('front/_home_page.html.twig', ['fiainana' => $fiainana, 'filtres' => $filtres,]);
    }

    /**
     * @Route("/message",name="home_message")
     *
     * @param Request      $request
     * @param Swift_Mailer $mailer
     *
     * @return JsonResponse
     */
    public function contact(Request $request, Swift_Mailer $mailer)
    {
        $email = $request->request->get('message')[0]['value'];
        $message = $request->request->get('message')[1]['value'];

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $swiftmessage = (new Swift_Message(self::SUBJECT))
                ->setFrom($email)
                ->setTo('julienrajerison5@gmail.com')
                ->setBody($message.'...'.$email);

            if (0 !== $mailer->send($swiftmessage)) {
                return new JsonResponse('success', Response::HTTP_OK);
            }
        }

        return new JsonResponse('invalid email', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param Request $request
     *
     * @Route("/inscription",name="inscription_members")
     *
     * @return Response
     */
    public function inscription(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            $user->setRoles([RoleConstant::ROLES['User']]);

            return $this->redirectToRoute('home_page');
        }

        return $this->render('front/_inscription.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/message/details/{id}",name="message_details")
     *
     * @param Fiainana $fiainana
     *
     * @return Response
     */
    public function details(Fiainana $fiainana)
    {
        $filtres = $this->manager->getRepository(Filtre::class)->findAll();

        return $this->render('front/_teny_details.html.twig', ['fiainana' => $fiainana, 'filtres' => $filtres]);
    }
}
