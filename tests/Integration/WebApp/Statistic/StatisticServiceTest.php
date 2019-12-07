<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Integration\WebApp\Season;

use App\Service\WebApp\Statistic\StatisticService;
use App\Tests\TestCase;

/**
 * @group integration
 */
class StatisticServiceTest extends TestCase
{
    protected $statisticService;

    protected function setUp()
    {
        parent::setUp();
        $this->statisticService = self::$container->get(StatisticService::class);
    }

    public function testGetLastUpdate()
    {
        $this->load(['tests/.fixtures/simpleTag.yml', 'tests/.fixtures/completeCategory.yml']);

        $result = $this->statisticService->getLastUpdate();

        $this->assertCount(6, $result);
    }

    public function testGetItems()
    {
        $this->load(['tests/.fixtures/simpleTag.yml', 'tests/.fixtures/completeCategory.yml']);

        $result = $this->statisticService->getItems();

        $this->assertArrayHasKey('tags', $result);
        $this->assertArrayHasKey('photos', $result);
        $this->assertArrayHasKey('categories', $result);
        $this->assertArrayHasKey('users', $result);
    }

    public function testGetStatisticsData()
    {
        $this->load('tests/.fixtures/completeCategory.yml');

        $result = $this->statisticService->getStatisticsData();

        $this->assertArrayHasKey('photo', $result);
        $this->assertArrayHasKey('category', $result);
    }
}