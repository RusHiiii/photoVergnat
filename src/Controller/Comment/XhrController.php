<?php

namespace App\Controller\Comment;

use App\Service\Tools\Error\Factory\ErrorFactory;
use App\Service\WebApp\Category\Exceptions\CategoryNotFoundException;
use App\Service\WebApp\Comment\CommentService;
use App\Service\WebApp\Comment\Exceptions\CommentInvalidDataException;
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
     * Création d'un commentaire
     * @Route("/xhr/app/comment/create", condition="request.isXmlHttpRequest()")
     */
    public function createComment(
        Request $request,
        CommentService $commentService
    ) {
        $data = $request->request->all();

        try {
            $resultCreate = $commentService->createComment($data['comment']);
        } catch (CommentInvalidDataException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                400
            );
        } catch (CategoryNotFoundException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                404
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($resultCreate, 'json', ['groups' => ['default']]),
            200
        );
    }
}
