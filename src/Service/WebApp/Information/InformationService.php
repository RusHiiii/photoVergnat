<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Information;

use App\Entity\WebApp\Comment;
use App\Repository\WebApp\Category\Doctrine\CategoryRepository;
use App\Repository\WebApp\User\Doctrine\UserRepository;
use App\Service\Tools\NotificationService;
use App\Service\WebApp\Information\Validator\InformationValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

class InformationService
{
    private $infoValidatorService;
    private $notificationService;
    private $userRepository;
    private $twig;

    public function __construct(
        InformationValidator $informationValidatorService,
        NotificationService $notificationService,
        UserRepository $userRepository,
        Environment $environment
    ) {
        $this->infoValidatorService = $informationValidatorService;
        $this->userRepository = $userRepository;
        $this->notificationService = $notificationService;
        $this->twig = $environment;
    }

    /**
     * Envoie du mail de contact
     * @param array $data
     * @return array
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendContactMail(array $data): array
    {
        /** Validation de la données */
        $validatedData = $this->infoValidatorService->checkContact($data, InformationValidator::TOKEN_SEND_MAIL);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors']
            ];
        }

        /** Récupération des users */
        $users = $this->userRepository->findByRole('%ADMIN%');

        /** Render et envoie du mail */
        $data = $this->twig->render('elements/shared/app/email/contact.html.twig', ['data' => $validatedData['data']]);
        $this->notificationService->sendEmail($users, NotificationService::MSG_SUBJECT_CONTACT, $data);

        return [
            'errors' => []
        ];
    }
}
