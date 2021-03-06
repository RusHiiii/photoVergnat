<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Integration\WebApp\Type;

use App\Entity\WebApp\Season;
use App\Entity\WebApp\Type;
use App\Repository\WebApp\Season\Doctrine\SeasonRepository;
use App\Repository\WebApp\Type\Doctrine\TypeRepository;
use App\Service\WebApp\Season\Exceptions\SeasonInvalidDataException;
use App\Service\WebApp\Season\SeasonService;
use App\Service\WebApp\Type\Exceptions\TypeInvalidDataException;
use App\Service\WebApp\Type\TypeService;
use App\Tests\TestCase;

/**
 * @group integration
 */
class TypeServiceTest extends TestCase
{
    protected $typeService;

    protected $typeRepository;

    protected function setUp()
    {
        parent::setUp();
        $this->typeService = self::$container->get(TypeService::class);
        $this->typeRepository = self::$container->get(TypeRepository::class);
    }

    public function testRemoveType()
    {
        $fixtures = $this->loadFile('tests/.fixtures/simpleType.yml');

        $result = $this->typeService->removeType($fixtures['type_1']);

        $this->assertTrue($result);
        $this->assertCount(0, $this->typeRepository->findAll());
    }

    public function testCreateSeasonWithBadTitle()
    {
        $type = [
            'title' => '',
        ];

        $this->expectException(TypeInvalidDataException::class);
        $this->typeService->createType($type);
    }

    public function testCreateType()
    {
        $type = [
            'title' => 'Panorama',
        ];

        $result = $this->typeService->createType($type);

        $this->assertInstanceOf(Type::class, $result);
        $this->assertEquals('Panorama', $result->getTitle());
    }

    public function testUpdateType()
    {
        $fixtures = $this->loadFile('tests/.fixtures/simpleType.yml');

        $type = [
            'title' => 'Panorama',
        ];

        $result = $this->typeService->updateType($type, $fixtures['type_1']);

        $this->assertInstanceOf(Type::class, $result);
        $this->assertEquals('Panorama', $result->getTitle());
        $this->assertNotNull($this->typeRepository->findByName('Panorama'));
    }

    public function testUpdateTypeWithBadTitle()
    {
        $fixtures = $this->loadFile('tests/.fixtures/simpleType.yml');

        $type = [
            'title' => '',
        ];

        $this->expectException(TypeInvalidDataException::class);
        $this->typeService->updateType($type, $fixtures['type_1']);
    }
}