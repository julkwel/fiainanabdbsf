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
use App\Repository\FiainanaRepository;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class FrontController.
 */
class FrontController extends AbstractBaseController
{
    const SUBJECT = 'Email avy amin\'ny mpanaraka ny fiainana be dia be';


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
     * @Route("/teny/api/", name="teny_api", methods={"GET"})
     * @param FiainanaRepository $tenyRepository
     *
     * @return Response
     * @throws Exception
     */
    public function list(FiainanaRepository $tenyRepository): Response
    {
        $_teny_list = $tenyRepository->findByDate();
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $_teny_api = [];
        foreach ($_teny_list as $key => $value) {
            $_teny_api[$key]['id'] = $value->getId();
            $_teny_api[$key]['description'] = preg_replace("/\s|&nbsp;/", ' ', strip_tags($value->getDescription()));
            $_teny_api[$key]['title'] = $value->getTitle();
            $_teny_api[$key]['image'] = $value->getAvatar();
            $_teny_api[$key]['dateajout'] = date_format($value->getDateAdd(), 'd-m-Y');
            $_teny_api[$key]['datepublication'] = date_format($value->getPublicationDate(), 'd-m-Y');
        }

        $_boo_user_list = $serializer->serialize($_teny_api, 'json');

        return $this->response($_boo_user_list);
    }

    /**
     * @Route("/",name="home_page")
     *
     * @return Response
     */
    public function home()
    {
        $filtres = $this->manager->getRepository(Filtre::class)->findAll();
        $fiainana = $this->manager->getRepository(Fiainana::class)->findBy(['isPublie' => true], ['id' => 'DESC'], 10);

        return $this->render('front/_home_page.html.twig', ['fiainana' => $fiainana, 'filtres' => $filtres,]);
    }

    /**
     * @param Request            $request
     * @param FiainanaRepository $repository
     *
     * @return Response
     *
     * @Route("/ajax/filter",name="ajax_filter")
     *
     */
    public function filter(Request $request, FiainanaRepository $repository)
    {
        $search = $request->query->get('search');

        $fiainana = $repository->findByAjax($search);

        return $this->render('front/_template_data.html.twig', ['fiainana' => $fiainana]);
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
            $user->setPassword($this->encoder->encodePassword($user, '123456'));
            $user->setRoles([RoleConstant::ROLES['User']]);

            return $this->redirectToRoute('home_page');
        }

        return $this->render('front/_inscription.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/message/details/{id}",name="message_details")
     *
     * @param Fiainana $fn
     *
     * @return Response
     */
    public function details(Fiainana $fn)
    {
        $fiainana = $this->manager->getRepository(Fiainana::class)->findBy(['isPublie' => true], ['id' => 'DESC'], 10);
        $filtres = $this->manager->getRepository(Filtre::class)->findAll();

        return $this->render('front/_teny_details.html.twig', ['fn' => $fn,'fiainana'=>$fiainana,'filtres' => $filtres]);
    }

    /**
     * @Route("/desabone/{id}",name="desabone")
     *
     * @param User $user
     *
     * @return RedirectResponse
     */
    public function desabone(User $user)
    {
        /** @var User $thisUser */
        $thisUser = $this->manager->getRepository(User::class)->find($user);

        $thisUser->setIsAbone(false);
        $this->manager->flush();

        return $this->redirectToRoute('home_page');
    }
}
