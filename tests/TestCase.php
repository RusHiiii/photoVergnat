<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests;

use App\Service\Tools\ToolsService;
use App\Service\Twig\ExtensionsService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TestCase extends KernelTestCase
{
    protected $loader;

    protected function setUp()
    {
        parent::setUp();
        self::bootKernel();

        $this->loader =  self::$container->get('fidry_alice_data_fixtures.loader.doctrine');
    }

    protected function loadFile($files)
    {
        $fixtureToLoad = $files;
        if (!is_array($files)) {
            $fixtureToLoad = [$files];
        }

        imagejpeg(imagecreatetruecolor(100, 100), 'tests/.fixtures/images/uploads/test_photovergnat_1.jpeg');
        imagejpeg(imagecreatetruecolor(100, 100), 'tests/.fixtures/images/uploads/test_photovergnat_2.jpeg');

        return $this->loader->load($fixtureToLoad);
    }

    protected function loadImage()
    {
        $file = tempnam(sys_get_temp_dir(), 'test');
        imagejpeg(imagecreatetruecolor(100, 100), $file);

        return $file;
    }

    protected function tearDown()
    {
        parent::tearDown();
        array_map( 'unlink', array_filter((array) glob("tests/.fixtures/images/uploads/*")));
    }
}