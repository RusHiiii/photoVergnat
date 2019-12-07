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

    protected $csrfToken;

    protected function setUp()
    {
        parent::setUp();
        self::bootKernel();

        $this->loader =  self::$container->get('fidry_alice_data_fixtures.loader.doctrine');
        $this->csrfToken =  self::$container->get('security.csrf.token_manager');
    }

    protected function load($files)
    {
        $fixtureToLoad = $files;
        if (!is_array($files)) {
            $fixtureToLoad = [$files];
        }

        return $this->loader->load($fixtureToLoad);
    }

    protected function getCsrfToken()
    {
        return $this->csrfToken;
    }
}