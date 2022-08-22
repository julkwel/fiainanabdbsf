<?php
/**
 * @author <julienrajerison5@gmail.com>
 */

namespace App\Command;

use App\Entity\User;
use App\Manager\DumpContactManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class CreateUserCommand.
 */
class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var DumpContactManager
     */
    private $dumpContactManager;
    /**
     * @var ParameterBagInterface
     */
    private $containerBag;

    /**
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param EntityManagerInterface       $entityManager
     * @param DumpContactManager           $dumpContactManager
     * @param ParameterBagInterface        $containerBag
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager, DumpContactManager $dumpContactManager, ParameterBagInterface $containerBag)
    {
        parent::__construct();
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->dumpContactManager = $dumpContactManager;
        $this->containerBag = $containerBag;
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->addOption('super-admin', null, InputOption::VALUE_NONE)
            ->addOption('dump', null, InputOption::VALUE_NONE)
            ->addOption('restore', null, InputOption::VALUE_NONE);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        if ($input->getOption('super-admin')) {
            try {
                $user = new User();
                $user->setPassword($this->userPasswordEncoder->encodePassword($user, 'fi@1n@na'));
                $user->setUsername("fiainanabediabe@gmail.com");
                $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
                $user->setIsAbone(true);
                $user->setNom("FIAINANA")->setPrenom("BE DIA BE");

                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $symfonyStyle->success("Create user superadmin done");
                exit(0);
            } catch (Exception $exception) {
                $symfonyStyle->error($exception->getMessage());
                exit(0);
            }
        }

        if ($input->getOption('dump')) {
            $symfonyStyle->success("Dump all mail");
            $this->dumpContactManager->getContact();
            $symfonyStyle->success("Dump all mail done");
            exit(0);
        }

        if ($input->getOption('restore')){
            $path = $this->containerBag->get('kernel.project_dir').'/public/upload/contact.yaml';
            $payload = Yaml::parse(file_get_contents($path));
            foreach ($payload as $item){
                $user = new User();
                $user->setRoles(['ROLE_USER']);
            }
            dd($payload);
        }

        $symfonyStyle->warning("Command not found");
    }
}
