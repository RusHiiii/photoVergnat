<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Integration\WebApp\Category;

use App\Entity\WebApp\Category;
use App\Entity\WebApp\Comment;
use App\Repository\WebApp\Category\Doctrine\CategoryRepository;
use App\Repository\WebApp\Comment\Doctrine\CommentRepository;
use App\Service\WebApp\Category\CategoryService;
use App\Service\WebApp\Category\Exceptions\CategoryInvalidDataException;
use App\Service\WebApp\Comment\CommentService;
use App\Service\WebApp\Comment\Exceptions\CommentInvalidDataException;
use App\Tests\TestCase;

/**
 * @group integration
 */
class CategoryServiceTest extends TestCase
{
    protected $categoryService;

    protected $categoryRepository;

    protected function setUp()
    {
        parent::setUp();
        $this->categoryService = self::$container->get(CategoryService::class);
        $this->categoryRepository = self::$container->get(CategoryRepository::class);
    }

    public function testRemoveCategory()
    {
        $fixtures = $this->loadFile('tests/.fixtures/completeCategory.yml');

        $result = $this->categoryService->removeCategory($fixtures['category_1']);

        $this->assertTrue($result);
        $this->assertCount(0, $this->categoryRepository->findAll());
    }

    public function testCreateCategoryWithBadData()
    {
        $fixture = $this->loadFile('tests/.fixtures/simpleUser.yml');

        $data = [
            'title' => '',
            'description' => 'description',
            'city' => 'Clermont',
            'lat' => '10.0325',
            'lng' => '10.0325',
            'season' => '1',
            'tags' => '[1]',
            'photos' => '[1]',
            'metaDescription' => 'test'
        ];

        $this->expectException(CategoryInvalidDataException::class);
        $this->categoryService->createCategory($data, $fixture['user_1']);
    }

    public function testCreateCategory()
    {
        $fixture = $this->loadFile('tests/.fixtures/completeCategory.yml');

        $data = [
            'title' => 'dezdzedzezde',
            'description' => 'description',
            'city' => 'Clermont',
            'lat' => '10.0325',
            'lng' => '10.0325',
            'season' => '1',
            'tags' => [1],
            'photos' => [1],
            'active' => true,
            'metaDescription' => 'test',
        ];

        $result = $this->categoryService->createCategory($data, $fixture['user_1']);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals('dezdzedzezde', $result->getTitle());
        $this->assertEquals('description', $result->getDescription());
        $this->assertEquals('Clermont', $result->getCity());
        $this->assertEquals('10.0325', $result->getLatitude());
        $this->assertEquals('10.0325', $result->getLongitude());
        $this->assertEquals('1', $result->getSeason()->getId());
        $this->assertCount(1, $result->getTags());
    }

    public function testUpdateCategory()
    {
        $fixture = $this->loadFile('tests/.fixtures/completeCategory.yml');

        $data = [
            'title' => 'edit',
            'description' => 'description',
            'city' => 'Clermont',
            'lat' => '10.0325',
            'lng' => '10.0325',
            'season' => '1',
            'tags' => [1],
            'photos' => [1],
            'active' => true,
            'metaDescription' => 'test'
        ];

        $result = $this->categoryService->updateCategory($data, $fixture['category_1']);

        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals('edit', $result->getTitle());
        $this->assertEquals('description', $result->getDescription());
        $this->assertEquals('Clermont', $result->getCity());
        $this->assertEquals('10.0325', $result->getLatitude());
        $this->assertEquals('10.0325', $result->getLongitude());
        $this->assertEquals('1', $result->getSeason()->getId());
        $this->assertCount(1, $result->getTags());
    }

    public function testUpdateCategoryWithBadData()
    {
        $fixture = $this->loadFile('tests/.fixtures/completeCategory.yml');

        $data = [
            'title' => '',
            'description' => 'description',
            'city' => 'Clermont',
            'lat' => '10.0325',
            'lng' => '10.0325',
            'season' => '1',
            'tags' => [1],
            'photos' => [1],
            'active' => true,
            'metaDescription' => 'test'
        ];

        $this->expectException(CategoryInvalidDataException::class);
        $this->categoryService->updateCategory($data, $fixture['category_1']);
    }

    public function testFilterPhotoByType()
    {
        $fixture = $this->loadFile('tests/.fixtures/completeCategory.yml');

        $result = $this->categoryService->filterPhotoByType($fixture['category_1']->getPhotos());

        $this->assertCount(1, $result);
    }

    public function testGetLastAction()
    {
        $this->loadFile('tests/.fixtures/completeCategory.yml');

        $result = $this->categoryService->getLastAction();

        $this->assertCount(1, $result);
    }

    public function testGetCategoriesOnline()
    {
        $this->loadFile('tests/.fixtures/completeCategory.yml');

        $result = $this->categoryService->getCategoriesOnline();

        $this->assertCount(2, $result);
    }

    public function testGetCategoriesPopularity()
    {
        $this->loadFile('tests/.fixtures/completeCategory.yml');

        $result = $this->categoryService->getCategoriesPopularity();

        $this->assertCount(1, $result);
    }
}