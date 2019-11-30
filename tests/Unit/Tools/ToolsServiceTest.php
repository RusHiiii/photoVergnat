<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Unit\Tools;

use App\Service\Tools\ToolsService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group unit
 */
class ToolsServiceTest extends KernelTestCase
{
    public function testTrimData()
    {
        $data = [
            'message' => ' je suis un test ',
            'id' => '5'
        ];

        $ToolsService = $this->getContainer()->get(ToolsService::class);

        $data = $ToolsService->trimData($data);
        $this->assertEquals('je suis un test', $data['message']);
        $this->assertEquals('5', $data['id']);
    }

    public function testSlugify()
    {
        $ToolsService = $this->getContainer()->get(ToolsService::class);

        $data = $ToolsService->slugify('zíefzefèá ');
        $this->assertEquals('zefzef-', $data);
    }

    public function testCompareByUpdated()
    {
        $ToolsService = $this->getContainer()->get(ToolsService::class);

        $begin = [
            'updated' => new \DateTime('now')
        ];

        $end = [
            'updated' => new \DateTime('2001-01-01')
        ];

        $result = $ToolsService->compareByUpdated($begin, $end);
        $this->assertFalse($result);
    }

    private function getContainer()
    {
        self::bootKernel();
        $container = self::$container;

        return $container;
    }
}