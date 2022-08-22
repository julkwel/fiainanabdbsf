<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>
 **/

namespace App\Controller\Admin;

use App\Controller\AbstractBaseController;
use App\Entity\Fiainana;
use App\Form\FiainanaType;
use App\Message\FiainanaNotification;
use DateTime;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Client\Common\HttpMethodsClient as HttpClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use OneSignal\Config;
use OneSignal\OneSignal;
use PhpParser\Node\Scalar\MagicConst\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FiainanaController.
 *
 * @Route("/admin")
 */
class FiainanaController extends AbstractBaseController
{
    public const NULL_IMAGE = 'https://scontent.ftnr4-1.fna.fbcdn.net/v/t1.0-9/78424265_551590798748326_1474012581050974208_o.jpg?_nc_cat=111&_nc_eui2=AeHILilGxrq0CALvPpICX9Iiq_Et_Js7tumJXGAmltk864dZmAhpNK8FeEZJQotIwR72MtDltFEQhFLI_HX9tEGzz34L_dtk8KhI9Kd42w-37g&_nc_ohc=XoF3bAz4drkAQkRHgyPv-YI0FFEapxLidzcpj2mje_BEJKteEStLRjhJw&_nc_ht=scontent.ftnr4-1.fna&oh=db4219c2bcb0dac056b91678a8ad6e07&oe=5E6A123A';

    /**
     * @Route("/fiainana",name="list_fiainana")
     *
     * @param Request             $request
     * @param MessageBusInterface $messageBus
     *
     * @return Response
     *
     * @throws Exception
     */
    public function listFiainana(Request $request, MessageBusInterface $messageBus)
    {
        $fiainana = $this->manager->getRepository(Fiainana::class)->findAll();
        $dateNow = new DateTime('now');

        /** @var Fiainana $message */
        foreach ($fiainana as $message) {
            if ($dateNow > $message->getPublicationDate() && false === $message->isPublie()) {
                $message->setIsPublie(true);
                $this->manager->flush();

                try {
                    $this->getOneSignal()->notifications->add([
                        'contents' => [
                            'en' => str_replace('zanaku', 'zanako', $message->getTitle()),
                        ],
                        'included_segments' => ['All'],
                        'send_after' => $message->getPublicationDate(),
                        'data' => ['fitiavana' => 'fiainana be dia be'],
                        "url" => $request->getSchemeAndHttpHost().$this->generateUrl('message_details', ['id' => $message->getId()]),
                    ]);
                } catch (Exception $exception) {
//                    dd($exception);
                }

                $messageBus->dispatch(new FiainanaNotification(['fiainana' => $message]));
            }
        }

        return $this->render('admin/fiainana/_list.html.twig', ['fs' => $fiainana]);
    }

    /**
     * @param Request  $request
     * @param Fiainana $fiainana
     *
     * @return Response
     *
     * @throws Exception
     * @Route("/manage/{id?}",name="manage_fiainana")
     */
    public function manage(Request $request, ?Fiainana $fiainana)
    {
        $fiainana = $fiainana ?: new Fiainana();
        $form = $this->createForm(FiainanaType::class, $fiainana);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('avatar')->getData();
            $date = $form->get('publicationDate')->getData();
            $fiainana->setPublicationDate($date);
            if (!empty($photo)) {
                $fileName = $this->upload($photo);
                $fiainana->setAvatar($request->getSchemeAndHttpHost().'/uploads/photos/'.$fileName);
            }

            if (empty($photo) && !$fiainana->getAvatar()) {
                $fiainana->setAvatar(self::NULL_IMAGE);
            }

            try {
                if (!$fiainana->getId()) {
                    $fiainana->setIsPublie(false);
                    $this->manager->persist($fiainana);
                }
                $this->manager->flush();
            } catch (Exception $exception) {
                dd($exception->getMessage());
            }

            return $this->redirectToRoute('list_fiainana');
        }

        return $this->render('admin/fiainana/_form.html.twig', ['form' => $form->createView(), 'fiainana' => $fiainana]);
    }

    /**
     * @Route("/remove/{id}",name="remove_fiainana")
     *
     * @param Fiainana $fiainana
     *
     * @return RedirectResponse
     */
    public function remove(Fiainana $fiainana)
    {
        if ($fiainana) {
            $this->manager->remove($fiainana);

            $this->manager->flush();
        }

        return $this->redirectToRoute('list_fiainana');
    }

    /**
     * @return OneSignal
     */
    public function getOneSignal()
    {
        $config = new Config();
        $config->setApplicationId('0eb6263e-44ed-4d51-8f6d-687cfe027f2c');
        $config->setApplicationAuthKey('ZDhmNjQ5OTYtZDYxMC00ZGJhLWJmMzMtN2Y2YzViYTc2YzFk');
        $config->setUserAuthKey('ZmE1ZjM5OTEtM2ZmMC00YzMyLTgxNzctM2YyNjY2Y2RiNjJh');

        $guzzle = new GuzzleClient([
            'base_uri' => 'https://www.fiainanabediabe.org',
            'timeout' => 2.0,
        ]);
        $client = new HttpClient(new GuzzleAdapter($guzzle), new GuzzleMessageFactory());
        $sender = new OneSignal($config, $client);

        return $sender;
    }
}
