<?php

namespace App\Controller\Information;

use App\Service\Tools\Error\Factory\ErrorFactory;
use App\Service\WebApp\Information\Exceptions\InformationInvalidDataException;
use App\Service\WebApp\Information\InformationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class XhrController extends AbstractController
{
    private $serializer;
    private $errorFactory;

    public function __construct(
        SerializerInterface $serializer,
        ErrorFactory $errorFactory
    ) {
        $this->serializer = $serializer;
        $this->errorFactory = $errorFactory;
    }

    /**
     * Envoie d'un message
     * @Route("/xhr/app/information/contact/send", condition="request.isXmlHttpRequest()")
     */
    public function sendContact(
        Request $request,
        InformationService $informationService
    ) {
        $data = $request->request->all();

        try {
            $informationService->sendContactMail($data['mail']);
        } catch (InformationInvalidDataException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                404
            );
        }

        return new JsonResponse([], 200);
    }
}
