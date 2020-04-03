<?php
/**
 * @author <Bocasay>.
 */

namespace App\Controller\Api;

use App\Controller\AbstractBaseController;
use App\Entity\User;
use App\Repository\FiainanaRepository;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ApiController.
 */
class ApiController extends AbstractBaseController
{

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
     * @param Request $request
     *
     * @Route("/api/inscription", name="manage_user_api")
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function userInscription(Request $request)
    {
        $user = new User();

        $date = new \DateTime($request->get('dateN'));
        $user->setBirthDate($date ?? new \DateTime('now'));
        $user->setRoles(['ROLE_USER']);
        $user->setUsername($request->get('username'));
        $user->setPassword($request->get('password'));
        $user->setNom($request->get('nom'));
        $user->setPrenom($request->get('prenom'));

        if (!$user->getId()) {
            $this->manager->persist($user);
        }
        $this->manager->flush();

        return new JsonResponse(['message' => 'success']);
    }
}
