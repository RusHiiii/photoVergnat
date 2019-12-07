<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Integration\WebApp\Season;

use App\Entity\WebApp\Season;
use App\Repository\WebApp\Season\Doctrine\SeasonRepository;
use App\Service\WebApp\Season\Exceptions\SeasonInvalidDataException;
use App\Service\WebApp\Season\SeasonService;
use App\Tests\TestCase;

/**
 * @group integration
 */
class SeasonServiceTest extends TestCase
{
    protected $seasonService;

    protected $seasonRepository;

    protected function setUp()
    {
        parent::setUp();
        $this->seasonService = self::$container->get(SeasonService::class);
        $this->seasonRepository = self::$container->get(SeasonRepository::class);
    }

    public function testRemoveSeason()
    {
        $fixtures = $this->load('tests/.fixtures/simpleSeason.yml');

        $result = $this->seasonService->removeSeason($fixtures['season_1']);

        $this->assertTrue($result);
        $this->assertCount(0, $this->seasonRepository->findAll());
    }

    public function testCreateSeasonWithBadToken()
    {
        $season = [
            'title' => 'Hiver',
            'token' => 'badToken'
        ];

        $this->expectException(SeasonInvalidDataException::class);
        $this->seasonService->createSeason($season);
    }

    public function testCreateSeasonWithBadTitle()
    {
        $season = [
            'title' => '',
            'token' => $this->getCsrfToken()->getToken('create-season')->getValue()
        ];

        $this->expectException(SeasonInvalidDataException::class);
        $this->seasonService->createSeason($season);
    }

    public function testCreateSeason()
    {
        $season = [
            'title' => 'Hiver',
            'token' => $this->getCsrfToken()->getToken('create-season')->getValue()
        ];

        $result = $this->seasonService->createSeason($season);

        $this->assertInstanceOf(Season::class, $result);
        $this->assertEquals('Hiver', $result->getTitle());
    }

    public function testUpdateSeason()
    {
        $fixtures = $this->load('tests/.fixtures/simpleSeason.yml');

        $season = [
            'title' => 'HiverEdit',
            'token' => $this->getCsrfToken()->getToken('update-season')->getValue()
        ];

        $result = $this->seasonService->updateSeason($season, $fixtures['season_1']);

        $this->assertInstanceOf(Season::class, $result);
        $this->assertEquals('HiverEdit', $result->getTitle());
        $this->assertNotNull($this->seasonRepository->findByName('HiverEdit'));
    }

    public function testUpdateSeasonWithBadTitle()
    {
        $fixtures = $this->load('tests/.fixtures/simpleSeason.yml');

        $season = [
            'title' => '',
            'token' => $this->getCsrfToken()->getToken('update-season')->getValue()
        ];

        $this->expectException(SeasonInvalidDataException::class);
        $this->seasonService->updateSeason($season, $fixtures['season_1']);
    }
}