<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Unit\Twig;

use App\Service\Twig\ExtensionsService;
use App\Tests\TestCase;

/**
 * @group unit
 */
class ExtensionsServiceTest extends TestCase
{
    protected $extensionsService;

    protected function setUp()
    {
        parent::setUp();
        $this->extensionsService = self::$container->get(ExtensionsService::class);
    }

    public function testSlugify()
    {
        $result = $this->extensionsService->slugify('refreéàfre');

        $this->assertEquals('refreeafre', $result);
    }

    public function testGetClassName()
    {
        $entities = $this->load('tests/.fixtures/completeCategory.yml');

        $result = $this->extensionsService->getClassName($entities['photo_1']);

        $this->assertCount(1, $result);
        $this->assertEquals('Urbain', $result[0]);
    }

    public function testGetAllClassName()
    {
        $entities = $this->load('tests/.fixtures/completeCategory.yml');

        $result = $this->extensionsService->getAllClassName([$entities['photo_1'], $entities['photo_2']]);

        $this->assertCount(2, $result);
        $this->assertEquals('Urbain', $result[0]);
        $this->assertEquals('Torrent', $result[1]);
    }

    public function testGetMainPhotoUrl()
    {
        $entities = $this->load('tests/.fixtures/completeCategory.yml');

        $result = $this->extensionsService->getMainPhotoUrl($entities['category_1'], 'Paysage');

        $this->assertEquals('1a844e6f9a651453654c7f39cd352fc89.jpeg', $result);
    }

    public function testHasRole()
    {
        $entities = $this->load('tests/.fixtures/simpleUser.yml');

        $result = $this->extensionsService->hasRole($entities['user_1'], 'ROLE_USER');

        $this->assertTrue($result);
    }
}