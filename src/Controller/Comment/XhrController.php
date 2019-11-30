<?php

namespace App\Controller\Comment;

use App\Controller\Security\Voter\CommentVoter;
use App\Entity\WebApp\Comment;
use App\Repository\WebApp\Category\Doctrine\CategoryRepository;
use App\Service\Tools\Error\Factory\ErrorFactory;
use App\Service\WebApp\Category\Exceptions\CategoryNotFoundException;
use App\Service\WebApp\Comment\CommentService;
use App\Service\WebApp\Comment\Exceptions\CommentInvalidDataException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
     * Suppression d'un commentaire
     * @Route("/xhr/admin/comment/remove/{id}", condition="request.isXmlHttpRequest()")
     */
    public function removeCategory(
        Request $request,
        CommentService $commentService,
        Comment $comment
    ) {
        $this->denyAccessUnlessGranted(CommentVoter::REMOVE, $comment);

        $commentService->removeComment($comment);

        return new JsonResponse([], 200);
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
            $this->serializer->serialize($resultCreate, 'json', ['groups' => ['default', 'comment']]),
            200
        );
    }

    /**
     * MàJ d'un commentaire
     * @Route("/xhr/admin/comment/update/{id}", condition="request.isXmlHttpRequest()")
     */
    public function updateCategory(
        Request $request,
        CommentService $commentService,
        Comment $comment
    ) {
        $this->denyAccessUnlessGranted(CommentVoter::EDIT, $comment);

        $data = $request->request->all();

        try {
            $resultUpdate = $commentService->updateComment($data['comment'], $comment);
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
            $this->serializer->serialize($resultUpdate, 'json', ['groups' => ['default', 'comment']]),
            200
        );
    }

    /**
     * Création d'un commentaire
     * @Route("/xhr/admin/comment/display/create/", condition="request.isXmlHttpRequest()")
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function displayModalCreate(
        Request $request,
        CategoryRepository $categoryRepository
    ) {
        $categories = $categoryRepository->findAll();

        return $this->render('comment/xhr/create.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * Edition d'un commentaire
     * @Route("/xhr/admin/comment/display/edit/{id}", condition="request.isXmlHttpRequest()")
     */
    public function displayModalEdit(
        Request $request,
        Comment $comment,
        CategoryRepository $categoryRepository
    ) {
        $this->denyAccessUnlessGranted(CommentVoter::EDIT, $comment);

        $categories = $categoryRepository->findAll();

        return $this->render('comment/xhr/edit.html.twig', [
            'comment' => $comment,
            'categories' => $categories
        ]);
    }
}
