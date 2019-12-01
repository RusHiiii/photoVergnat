<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Unit\Twig;

use App\Service\Tools\ToolsService;
use App\Service\Twig\ExtensionsService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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

    public function testGetClassName()
    {
        $entities = $this->loader->load(['tests/.fixtures/completeCategory.yml']);

        $ExtensionsService = $this->getContainer()->get(ExtensionsService::class);

        $result = $ExtensionsService->getClassName($entities['photo_1']);
        $this->assertCount(1, $result);
        $this->assertEquals('Urbain', $result[0]);
    }

    public function testGetAllClassName()
    {
        $entities = $this->loader->load(['tests/.fixtures/completeCategory.yml']);

        $ExtensionsService = $this->getContainer()->get(ExtensionsService::class);

        $result = $ExtensionsService->getAllClassName([$entities['photo_1'], $entities['photo_2']]);
        $this->assertCount(2, $result);
        $this->assertEquals('Urbain', $result[0]);
        $this->assertEquals('Torrent', $result[1]);
    }

    public function testGetMainPhotoUrl()
    {
        $entities = $this->loader->load(['tests/.fixtures/completeCategory.yml']);

        $ExtensionsService = $this->getContainer()->get(ExtensionsService::class);

        $result = $ExtensionsService->getMainPhotoUrl($entities['category_1'], 'Paysage');
        $this->assertEquals('1a844e6f9a651453654c7f39cd352fc89.jpeg', $result);
    }

    public function testHasRole()
    {
        $entities = $this->loader->load(['tests/.fixtures/simpleUser.yml']);

        $ExtensionsService = $this->getContainer()->get(ExtensionsService::class);

        $result = $ExtensionsService->hasRole($entities['user_1'], 'ROLE_USER');
        $this->assertTrue($result);
    }

    private function getContainer()
    {
        self::bootKernel();
        $container = self::$container;

        return $container;
    }
}