<?php
/**
 * @author <julienrajerison5@gmail.com>
 */

namespace App\Manager;

use App\Message\NotificationHandler;
use App\Repository\FiainanaRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class FiainanaManager.
 */
class FiainanaManager
{
    /**
     * @var FiainanaRepository
     */
    private $fiainanaRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var Environment
     */
    private $environment;
    /**
     * @var Swift_Mailer
     */
    private $mailer;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param FiainanaRepository     $fiainanaRepository
     * @param UserRepository         $userRepository
     * @param EntityManagerInterface $entityManager
     * @param ParameterBagInterface  $parameterBag
     * @param Environment            $environment
     * @param Swift_Mailer           $mailer
     * @param RouterInterface        $router
     */
    public function __construct(FiainanaRepository $fiainanaRepository, UserRepository $userRepository, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag, Environment $environment, Swift_Mailer $mailer, RouterInterface $router)
    {
        $this->fiainanaRepository = $fiainanaRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
        $this->environment = $environment;
        $this->mailer = $mailer;
        $this->router = $router;
    }

    /**
     * @param SymfonyStyle $symfonyStyle
     * @param string       $sendTo
     */
    public function sendTo(SymfonyStyle $symfonyStyle, string $sendTo)
    {
        $transport = $this->mailer->getTransport();
        if (!$transport->ping()) {
            $transport->stop();
            $transport->start();
        }

        $member = $this->userRepository->findOneBy(['username' => $sendTo]);
        if (!$member) {
            $symfonyStyle->error("Member not found !!");
            exit(0);
        }

        $symfonyStyle->note("User content loading ...");
        $this->generateContent($symfonyStyle, $member);
    }

    /**
     * @param SymfonyStyle  $symfonyStyle
     * @param UserInterface $member
     * @param bool          $toView
     */
    public function generateContent(SymfonyStyle $symfonyStyle, UserInterface $member, bool $toView = false)
    {
        try {
            $messages = $this->fiainanaRepository->findBy(['isSended' => null]);
            $symfonyStyle->note(sprintf("%s messages found", count($messages)));
            
            foreach ($messages as $message) {
                $desc = preg_replace("/\s|&nbsp;/", ' ', strip_tags($message->getDescription()));
                $template = $this->environment->render(
                    'admin/email/_email_template.html.twig',
                    [
                        'title' => str_replace('zanaku', $member->getPrenom(), $message->getTitle()),
                        'message' => str_replace('zanaku', $member->getPrenom(), $desc),
                        'image' => $message->getAvatar(),
                        'urldes' => 'https://fiainanabediabe.org/'.$this->router->generate('desabone', ['id' => $member->getId()]),
                        'member' => $member,
                    ]
                );

                if (filter_var($member->getUsername(), FILTER_VALIDATE_EMAIL)) {
                    $swiftmessage = (new Swift_Message(NotificationHandler::SUBJECT))
                        ->setFrom($this->parameterBag->get('email_fiainana'))
                        ->setTo($member->getUsername())
                        ->setBody($template, 'text/html');
                    $this->mailer->send($swiftmessage);

                    $symfonyStyle->success("Mail sended !!!");
                }

                if ($toView) {
                    $message->setIsSended(true);
                    $this->entityManager->flush();
                }
            }
        } catch (Exception $exception) {
            $symfonyStyle->error("Une erreur est survenu :".$exception->getMessage());
        }
    }

    /**
     * @param SymfonyStyle $symfonyStyle
     */
    public function sendAll(SymfonyStyle $symfonyStyle)
    {
        $users = $this->userRepository->findBy(['isAbone' => true]);
        $symfonyStyle->note("sending date :".(new DateTime('now'))->format("d-m-Y H:i"));
        $progressBar = $symfonyStyle->createProgressBar(count($users));
        foreach ($users as $user) {
            $progressBar->advance();
            $this->generateContent($symfonyStyle, $user, true);
        }

        $symfonyStyle->note("Done sending date :".(new DateTime('now'))->format("d-m-Y H:i"));
        $progressBar->finish();
    }
}
