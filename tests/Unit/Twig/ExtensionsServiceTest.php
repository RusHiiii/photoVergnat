<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Unit\Tools\Twig;

use App\Service\Tools\ToolsService;
use App\Service\Twig\ExtensionsService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group unit
 */
class ExtensionsServiceTest extends KernelTestCase
{
    private $loader;

    protected function setUp()
    {
        $this->loader = $this->getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
    }

    public function testSlugify()
    {
        $ExtensionsService = $this->getContainer()->get(ExtensionsService::class);

        $result = $ExtensionsService->slugify('refreéàfre');
        $this->assertEquals('refreeafre', $result);
    }

    private function getContainer()
    {
        self::bootKernel();
        $container = self::$container;

        return $container;
    }
}