<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>
 **/

namespace App\Message;

use App\Entity\Fiainana;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Class NotificationHandler.
 */
class NotificationHandler implements MessageHandlerInterface
{
    public const SUBJECT = 'Fiainana be dia be';
    private $userRepos;
    private $twig;
    private $mailer;
    private $parameter;
    private $logger;
    private $router;

    /**
     * NotificationHandler constructor.
     *
     * @param UserRepository        $userRepository
     * @param Environment           $environment
     * @param Swift_Mailer          $mailer
     * @param ParameterBagInterface $parameterBag
     * @param LoggerInterface       $logger
     */
    public function __construct(UserRepository $userRepository, Environment $environment, Swift_Mailer $mailer, ParameterBagInterface $parameterBag, LoggerInterface $logger, RouterInterface $router)
    {
        $this->userRepos = $userRepository;
        $this->twig = $environment;
        $this->mailer = $mailer;
        $this->parameter = $parameterBag;
        $this->logger = $logger;
        $this->router = $router;
    }

    /**
     * @param FiainanaNotification $notification
     */
    public function __invoke(FiainanaNotification $notification)
    {
        $fiainana = $notification->getContent();
        $members = $this->userRepos->findAll();

        /** @var Fiainana $message */
        $message = $fiainana['fiainana'];
        $desc = preg_replace("/\s|&nbsp;/", ' ', strip_tags($message->getDescription()));

        try {
            foreach ($members as $member) {
                $transport = $this->mailer->getTransport();
                if (!$transport->ping()) {
                    $transport->stop();
                    $transport->start();
                }

                $template = $this->twig->render(
                    'admin/email/_email_template.html.twig',
                    [
                        'title' => str_replace('zanaku', $member->getPrenom(), $message->getTitle()),
                        'message' => str_replace('zanaku', $member->getPrenom(), $desc),
                        'image' => $message->getAvatar(),
                        'urldes' => 'https://www.fiainanabediabe.org/'.$this->router->generate('desabone', ['id' => $member->getId()]),
                        'member' => $member,
                    ]
                );

                if (filter_var($member->getUsername(), FILTER_VALIDATE_EMAIL)) {
                    $swiftmessage = (new Swift_Message(self::SUBJECT))
                        ->setFrom($this->parameter->get('email_fiainana'))
                        ->setTo($member->getUsername())
                        ->setBody($template, 'text/html');
                    $this->mailer->send($swiftmessage);
                }
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}