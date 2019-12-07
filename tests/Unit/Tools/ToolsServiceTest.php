<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Unit\Tools;

use App\Service\Tools\ToolsService;
use App\Tests\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group unit
 */
class ToolsServiceTest extends TestCase
{
    protected $toolsService;

    protected function setUp()
    {
        parent::setUp();
        $this->toolsService = self::$container->get(ToolsService::class);
    }

    public function testTrimData()
    {
        $data = [
            'message' => ' je suis un test ',
            'id' => '5'
        ];

        $data = $this->toolsService->trimData($data);

        $this->assertEquals('je suis un test', $data['message']);
        $this->assertEquals('5', $data['id']);
    }

    public function testSlugify()
    {
        $data = $this->toolsService->slugify('zíefzefèá ');

        $this->assertEquals('zefzef-', $data);
    }

    public function testCompareByUpdated()
    {
        $begin = [
            'updated' => new \DateTime('now')
        ];

        $end = [
            'updated' => new \DateTime('2001-01-01')
        ];

        $result = $this->toolsService->compareByUpdated($begin, $end);

        $this->assertFalse($result);
    }
}