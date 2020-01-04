<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Integration\WebApp\Photo;

use App\Entity\WebApp\Comment;
use App\Entity\WebApp\Photo;
use App\Entity\WebApp\Season;
use App\Repository\WebApp\Comment\Doctrine\CommentRepository;
use App\Repository\WebApp\Photo\Doctrine\PhotoRepository;
use App\Repository\WebApp\Season\Doctrine\SeasonRepository;
use App\Service\WebApp\Comment\CommentService;
use App\Service\WebApp\Comment\Exceptions\CommentInvalidDataException;
use App\Service\WebApp\Photo\Exceptions\PhotoInvalidDataException;
use App\Service\WebApp\Photo\PhotoService;
use App\Service\WebApp\Season\Exceptions\SeasonInvalidDataException;
use App\Service\WebApp\Season\SeasonService;
use App\Service\WebApp\Type\Exceptions\TypeNotFoundException;
use App\Tests\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @group integration
 */
class PhotoServiceTest extends TestCase
{
    protected $photoService;

    protected $photoRepository;

    protected $file;

    protected function setUp()
    {
        parent::setUp();
        $this->photoService = self::$container->get(PhotoService::class);
        $this->photoRepository = self::$container->get(PhotoRepository::class);
    }

    public function testRemovePhoto()
    {
        $fixtures = $this->loadFile('tests/.fixtures/completeCategory.yml');

        $result = $this->photoService->removePhoto($fixtures['photo_1']);

        $this->assertTrue($result);
        $this->assertCount(1, $this->photoRepository->findAll());
    }

    public function testCreatePhoto()
    {
        $image = new UploadedFile($this->loadImage(),'image.jpeg','image/jpeg',null,true);

        $data = [
            'format' => '1',
            'tags' => ['1'],
            'title' => 'Puy Pariou',
        ];

        $result = $this->photoService->createPhoto($data, $image);
        $this->assertInstanceOf(Photo::class, $result);
        $this->assertEquals('Puy Pariou', $result->getTitle());
    }

    public function testUpdatePhoto()
    {
        $image = new UploadedFile($this->loadImage(),'image.jpeg','image/jpeg',null,true);
        $fixtures = $this->loadFile('tests/.fixtures/completeCategory.yml');

        $data = [
            'format' => '1',
            'tags' => ['1'],
            'title' => 'Puy Pariou Edit',
        ];

        $result = $this->photoService->updatePhoto($data, $image, $fixtures['photo_1']);

        $this->assertInstanceOf(Photo::class, $result);
        $this->assertEquals('Puy Pariou Edit', $result->getTitle());
    }

    public function testUpdatePhotoWithoutFile()
    {
        $fixtures = $this->loadFile('tests/.fixtures/completeCategory.yml');

        $data = [
            'format' => '1',
            'tags' => ['1'],
            'title' => 'Puy Pariou Edit',
        ];

        $result = $this->photoService->updatePhoto($data, null, $fixtures['photo_1']);

        $this->assertInstanceOf(Photo::class, $result);
        $this->assertEquals('Puy Pariou Edit', $result->getTitle());
    }

    public function testUpdatePhotoWithBadData()
    {
        $fixtures = $this->loadFile('tests/.fixtures/completeCategory.yml');

        $data = [
            'format' => '10',
            'tags' => ['1'],
            'title' => 'Puy Pariou Edit',
        ];

        $this->expectException(TypeNotFoundException::class);
        $this->photoService->updatePhoto($data, null, $fixtures['photo_1']);
    }

    public function testGetLastAction()
    {
        $this->loadFile('tests/.fixtures/completeCategory.yml');

        $result = $this->photoService->getLastAction();

        $this->assertCount(2, $result);
    }

    public function testGetNumberPhotoByMonth()
    {
        $this->loadFile('tests/.fixtures/completeCategory.yml');

        $result = $this->photoService->getNumberPhotoByMonth();

        $this->assertCount(5, $result);
    }
}