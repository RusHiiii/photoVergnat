<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\Tools;

use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    const MSG_EMAIL_SEND = 'Email envoyé !';
    const MSG_SUBJECT_CONTACT = 'Demande de contact';

    private $mailer;
    private $config;

    public function __construct(
        \Swift_Mailer $mailer,
        array $config
    ) {
        $this->mailer = $mailer;
        $this->config = $config;
    }

    /**
     * Envoie du mail
     * @param array $users
     * @param string $subject
     * @param mixed $message
     * @return array
     */
    public function sendEmail(array $users, string $subject, $message): array
    {
        $from = [$this->config['user'] => 'Photo\'Vergnat - Contact'];
        $bcc = ['damiens.florent@orange.fr'];

        $mails = $this->formatMailFromUsers($users);

        $message = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setBcc($bcc)
            ->setTo($mails)
            ->setBody(
                $message,
                'text/html'
            );

        $this->mailer->send($message);

        return [
            'msg' => self::MSG_EMAIL_SEND,
            'to' => $mails
        ];
    }

    /**
     * Formatage des users
     * @param array $users
     * @return array
     */
    private function formatMailFromUsers(array $users): array
    {
        $mails = [];

        foreach ($users as $user) {
            $mails[] = $user->getEmail();
        }

        return $mails;
    }
}
