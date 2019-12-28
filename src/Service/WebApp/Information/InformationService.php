<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Information;

use App\Repository\WebApp\User\Doctrine\UserRepository;
use App\Service\Tools\NotificationService;
use App\Service\WebApp\Information\Exceptions\InformationInvalidDataException;
use App\Service\WebApp\Information\Validator\InformationValidator;
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
     * @return bool
     * @throws InformationInvalidDataException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendContactMail(array $data): bool
    {
        /** Validation de la données */
        $validatedData = $this->infoValidatorService->checkContact($data);
        if (count($validatedData['errors']) > 0) {
            throw new InformationInvalidDataException($validatedData['errors'], InformationInvalidDataException::COMMENT_INVALID_DATA_MESSAGE);
        }

        /** Récupération des users */
        $users = $this->userRepository->findByRole('%ADMIN%');

        /** Render et envoie du mail */
        $data = $this->twig->render('elements/shared/app/email/contact.html.twig', ['data' => $validatedData['data']]);
        $this->notificationService->sendEmail($users, NotificationService::MSG_SUBJECT_CONTACT, $data);

        return true;
    }
}
