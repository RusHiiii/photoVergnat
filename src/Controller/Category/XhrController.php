<?php

namespace App\Controller\Category;

use App\Controller\Security\Voter\CategoryVoter;
use App\Entity\WebApp\Category;
use App\Repository\WebApp\Photo\Doctrine\PhotoRepository;
use App\Repository\WebApp\Season\Doctrine\SeasonRepository;
use App\Repository\WebApp\Tag\Doctrine\TagRepository;
use App\Service\Tools\Error\Factory\ErrorFactory;
use App\Service\WebApp\Category\CategoryService;
use App\Service\WebApp\Category\Exceptions\CategoryInvalidDataException;
use App\Service\WebApp\Category\Exceptions\CategoryNotFoundException;
use App\Service\WebApp\Photo\Exceptions\PhotoNotFoundException;
use App\Service\WebApp\Season\Exceptions\SeasonNotFoundException;
use App\Service\WebApp\Tag\Exceptions\TagNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * Suppression d'une catégorie
     * @Route("/xhr/admin/category/remove/{id}", condition="request.isXmlHttpRequest()")
     */
    public function removeCategory(
        Request $request,
        CategoryService $categoryService,
        Category $category
    ) {
        $this->denyAccessUnlessGranted(CategoryVoter::REMOVE, $category);

        $categoryService->removeCategory($category);

        return new JsonResponse([], 200);
    }

    /**
     * MàJ d'une catégorie
     * @Route("/xhr/admin/category/update/{id}", condition="request.isXmlHttpRequest()")
     */
    public function updateCategory(
        Request $request,
        CategoryService $categoryService,
        Category $category
    ) {
        $this->denyAccessUnlessGranted(CategoryVoter::EDIT, $category);

        $data = $request->request->all();

        try {
            $resultUpdate = $categoryService->updateCategory($data['category'], $category);
        } catch (CategoryInvalidDataException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                400
            );
        } catch (CategoryNotFoundException | PhotoNotFoundException | SeasonNotFoundException | TagNotFoundException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                404
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($resultUpdate, 'json', ['groups' => ['default', 'category']]),
            200
        );
    }

    /**
     * Création d'une categorie
     * @Route("/xhr/admin/category/create", condition="request.isXmlHttpRequest()")
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function createCategory(
        Request $request,
        CategoryService $categoryService
    ) {
        $data = $request->request->all();

        try {
            $resultCreate = $categoryService->createCategory($data['category']);
        } catch (CategoryInvalidDataException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                400
            );
        } catch (PhotoNotFoundException | SeasonNotFoundException | TagNotFoundException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                404
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($resultCreate, 'json', ['groups' => ['default', 'category']]),
            200
        );
    }

    /**
     * Création d'une catégorie
     * @Route("/xhr/admin/category/display/create/", condition="request.isXmlHttpRequest()")
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function displayModalCreate(
        Request $request,
        TagRepository $tagRepository,
        SeasonRepository $seasonRepository,
        PhotoRepository $photoRepository
    ) {
        $tags = $tagRepository->findByType('category');
        $seasons = $seasonRepository->findAll();
        $photos = $photoRepository->findByUnused();

        return $this->render('category/xhr/create.html.twig', [
            'tags' => $tags,
            'seasons' => $seasons,
            'photos' => $photos
        ]);
    }

    /**
     * Edition d'une catégorie
     * @Route("/xhr/admin/category/display/edit/{id}", condition="request.isXmlHttpRequest()")
     */
    public function displayModalEdit(
        Request $request,
        TagRepository $tagRepository,
        SeasonRepository $seasonRepository,
        PhotoRepository $photoRepository,
        Category $category
    ) {
        $this->denyAccessUnlessGranted(CategoryVoter::EDIT, $category);

        $tags = $tagRepository->findByType('category');
        $seasons = $seasonRepository->findAll();
        $photos = $photoRepository->findByUnusedAndCategory($category);

        return $this->render('category/xhr/edit.html.twig', [
            'tags' => $tags,
            'seasons' => $seasons,
            'photos' => $photos,
            'category' => $category
        ]);
    }
}
