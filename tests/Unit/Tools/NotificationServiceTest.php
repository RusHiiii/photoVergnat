<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Unit\Tools;

use App\Service\Tools\NotificationService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group unit
 */
class NotificationServiceTest extends KernelTestCase
{
    private $loader;

    protected function setUp()
    {
        $this->loader = $this->getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
    }

    public function testSendMail()
    {
        $entities = $this->loader->load(['tests/.fixtures/simpleUser.yml']);

        $NotificationService = $this->getContainer()->get(NotificationService::class);

        $result = $NotificationService->sendEmail([$entities['user_1']], 'subject', 'message');
        $this->assertCount(2, $result);
        $this->assertEquals('Email envoyé !', $result['msg']);
        $this->assertCount(1, $result['to']);
    }

    private function getContainer()
    {
        self::bootKernel();
        $container = self::$container;

        return $container;
    }
}