<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>
 **/

namespace App\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class DumpContact
{
    public function getContact(ContainerInterface $parameterBag)
    {
        $path = $parameterBag->getParameter('kernel.project_dir').'/public/upload/contact.yaml';

        if (function_exists('imap_open')) {
            $hostname = '{imap.gmail.com:993/imap/ssl}[Gmail]/Messages envoy&AOk-s';
            $email = $parameterBag->getParameter('email_fiainana');
            $pass = $parameterBag->getParameter('pass_fiainana');

            $inbox = imap_open($hostname, $email, $pass) or die('Cannot connect to Gmail: '.imap_last_error());
            $emails = imap_search($inbox, 'ALL');

            $contact = [];

            /* if emails are returned, cycle through each... */
            if ($emails) {
                /* put the newest emails on top */
                rsort($emails);

                foreach ($emails as $email_number) {

                    /* get information specific to this email */
                    $overview = imap_fetch_overview($inbox, $email_number, 0);
                    $message = imap_fetchbody($inbox, $email_number,1);
                    dd($message);
                    $contact[$overview[0]->to] = $overview[0]->to;
                }
            }
            $yaml = Yaml::dump($contact);
            file_put_contents($path, $yaml);

            imap_close($inbox);
        }
    }
}