<?php

namespace App\Controller\Category;

use App\Controller\Security\Voter\CategoryVoter;
use App\Entity\WebApp\Category;
use App\Repository\WebApp\Photo\Doctrine\PhotoRepository;
use App\Repository\WebApp\Season\Doctrine\SeasonRepository;
use App\Repository\WebApp\Tag\Doctrine\TagRepository;
use App\Service\WebApp\Category\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class XhrController extends AbstractController
{
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

        $resultRemove = $categoryService->removeCategory($category);

        return new JsonResponse([
            'errors' => $resultRemove['errors']
        ]);
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
        $resultUpdate = $categoryService->updateCategory($data['category'], $category);

        return new JsonResponse([
            'errors' => $resultUpdate['errors'],
            'category' => $resultUpdate['category']
        ]);
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
        $resultCreate = $categoryService->createCategory($data['category']);

        return new JsonResponse([
            'errors' => $resultCreate['errors'],
            'category' => $resultCreate['category']
        ]);
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
}
