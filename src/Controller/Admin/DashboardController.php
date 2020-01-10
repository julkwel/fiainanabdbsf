<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>
 **/

namespace App\Controller\Admin;

use App\Constant\RoleConstant;
use App\Controller\AbstractBaseController;
use App\Entity\Fiainana;
use App\Entity\User;
use App\Repository\FiainanaRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
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
    private $parameter;

    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder, ParameterBagInterface $parameterBag)
    {
        parent::__construct($manager, $passwordEncoder);
        $this->parameter = $parameterBag;
    }

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
        $path = $this->parameter->get('kernel.project_dir').'/public/upload/message.yaml';

        if (function_exists('imap_open')) {
            $hostname = '{imap.gmail.com:993/imap/ssl}[Gmail]/Messages envoy&AOk-s';
            $email = $this->parameter->get('email_fiainana');
            $pass = $this->parameter->get('pass_fiainana');

            $inbox = imap_open($hostname, $email, $pass) or die('Cannot connect to Gmail: '.imap_last_error());
            $emails = imap_search($inbox, 'ALL');

            $mess = [];
            $prenom = [];

            /* if emails are returned, cycle through each... */
            if ($emails) {
                /* put the newest emails on top */
                rsort($emails);

                foreach ($emails as $i => $email_number) {
                    $overview = imap_fetch_overview($inbox, $email_number, 0);
                    $bodyText = imap_fetchbody($inbox, $email_number, 1.2);
                    if (($bodyText === '') > 0) {
                        $bodyText = imap_fetchbody($inbox, $email_number, 1);
                    }

                    $dom = new Crawler($bodyText);

                    foreach ($dom->filter('p') as $z => $p) {
                        $mess[$z] = $p->nodeValue;
                    }
                    if (isset($mess[2])){
                        $prenom[$overview[0]->to][$mess[2]] = preg_replace('/\s+/','',$mess[2]);
                    }
                }
            }

            $yaml = Yaml::dump($prenom);
            file_put_contents($path, $yaml);

            imap_close($inbox);
        }
        $users = $userRepository->findMembers();
        $fiainana = $fiainanaRepository->findAll();

        return $this->render(
            'admin/dashboard/_dashboard.html.twig',
            ['users' => count($users), 'fiainana' => count($fiainana),]
        );
    }
}