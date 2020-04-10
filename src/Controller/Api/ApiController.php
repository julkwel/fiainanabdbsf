<?php
/**
 * @author <Bocasay>.
 */

namespace App\Controller\Api;

use App\Controller\AbstractBaseController;
use App\Entity\User;
use App\Repository\FiainanaRepository;
use App\Repository\TosikaRepository;
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
        $tenyData = $tenyRepository->findByDate();
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $tenyList = [];
        foreach ($tenyData as $key => $value) {
            $tenyList[$key]['id'] = $value->getId();
            $tenyList[$key]['description'] = preg_replace("/\s|&nbsp;/", ' ', strip_tags($value->getDescription()));
            $tenyList[$key]['title'] = $value->getTitle();
            $tenyList[$key]['image'] = $value->getAvatar();
            $tenyList[$key]['dateajout'] = date_format($value->getDateAdd(), 'd-m-Y');
            $tenyList[$key]['datepublication'] = date_format($value->getPublicationDate(), 'd-m-Y');
        }
        $tenyList = $serializer->serialize($tenyList, 'json');

        return $this->response($tenyList);
    }


    /**
     * @Route("/tosika/api/", name="tosika_api", methods={"GET"})
     *
     * @param TosikaRepository $tosikaRepository
     *
     * @return Response
     */
    public function listTosika(TosikaRepository $tosikaRepository): Response
    {
        $tosika = $tosikaRepository->findAll();
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $tosikaList = [];
        foreach ($tosika as $key => $value) {
            $tosikaList[$key]['id'] = $value->getId();
            $tosikaList[$key]['message'] = $value->getMessage();
            $tosikaList[$key]['dateAdd'] = $value->getDateAdd() ? $value->getDateAdd()->format('d-m-Y') : (new \DateTime('now'))->format('d-m-Y');
        }
        $tosikaData = $serializer->serialize($tosikaList, 'json');

        return $this->response($tosikaData);
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
