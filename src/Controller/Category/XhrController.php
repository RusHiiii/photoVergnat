<?php

namespace App\Controller\Category;

use App\Controller\Security\Voter\CategoryVoter;
use App\Entity\Category;
use App\Repository\PhotoRepository;
use App\Repository\SeasonRepository;
use App\Repository\TagRepository;
use App\Service\Category\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class XhrController extends AbstractController
{
    /**
     * Création d'une catégorie
     * @Route("/xhr/admin/category/display/create/", condition="request.isXmlHttpRequest()")
     */
    public function displayModalCreate(
        Request $request,
        TagRepository $tagRepository,
        SeasonRepository $seasonRepository,
        PhotoRepository $photoRepository
    ) {
        $this->denyAccessUnlessGranted(CategoryVoter::VIEW, Category::class);

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
        $this->denyAccessUnlessGranted(CategoryVoter::EDIT, Category::class);

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

    /**
     * Création d'une categorie
     * @Route("/xhr/admin/category/create", condition="request.isXmlHttpRequest()")
     */
    public function createCategory(
        Request $request,
        CategoryService $categoryService
    ) {
        $this->denyAccessUnlessGranted(CategoryVoter::CREATE, Category::class);

        $data = $request->request->all();
        $resultCreate = $categoryService->createCategory($data['category']);

        return new JsonResponse([
            'errors' => $resultCreate['errors'],
            'category' => $resultCreate['category']
        ]);
    }

    /**
     * Suppression d'une catégorie
     * @Route("/xhr/admin/category/remove", condition="request.isXmlHttpRequest()")
     */
    public function removeCategory(
        Request $request,
        CategoryService $categoryService
    ) {
        $this->denyAccessUnlessGranted(CategoryVoter::REMOVE, Category::class);

        $data = $request->request->all();
        $resultRemove = $categoryService->removeCategory($data['category']);

        return new JsonResponse([
            'errors' => $resultRemove['errors']
        ]);
    }
}
