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
        $fixtures = $this->load('tests/.fixtures/simpleType.yml');

        $result = $this->typeService->removeType($fixtures['type_1']);

        $this->assertTrue($result);
        $this->assertCount(0, $this->typeRepository->findAll());
    }

    public function testCreateTypeWithBadToken()
    {
        $type = [
            'title' => 'Paysage',
            'token' => 'badToken'
        ];

        $this->expectException(TypeInvalidDataException::class);
        $this->typeService->createType($type);
    }

    public function testCreateSeasonWithBadTitle()
    {
        $type = [
            'title' => '',
            'token' => $this->getCsrfToken()->getToken('create-type')->getValue()
        ];

        $this->expectException(TypeInvalidDataException::class);
        $this->typeService->createType($type);
    }

    public function testCreateType()
    {
        $type = [
            'title' => 'Panorama',
            'token' => $this->getCsrfToken()->getToken('create-type')->getValue()
        ];

        $result = $this->typeService->createType($type);

        $this->assertInstanceOf(Type::class, $result);
        $this->assertEquals('Panorama', $result->getTitle());
    }

    public function testUpdateType()
    {
        $fixtures = $this->load('tests/.fixtures/simpleType.yml');

        $type = [
            'title' => 'Panorama',
            'token' => $this->getCsrfToken()->getToken('update-type')->getValue()
        ];

        $result = $this->typeService->updateType($type, $fixtures['type_1']);

        $this->assertInstanceOf(Type::class, $result);
        $this->assertEquals('Panorama', $result->getTitle());
        $this->assertNotNull($this->typeRepository->findByName('Panorama'));
    }

    public function testUpdateTypeWithBadTitle()
    {
        $fixtures = $this->load('tests/.fixtures/simpleType.yml');

        $type = [
            'title' => '',
            'token' => $this->getCsrfToken()->getToken('update-type')->getValue()
        ];

        $this->expectException(TypeInvalidDataException::class);
        $this->typeService->updateType($type, $fixtures['type_1']);
    }
}