<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Unit\Tools;

use App\Service\Tools\NotificationService;
use App\Tests\TestCase;

/**
 * @group unit
 */
class NotificationServiceTest extends TestCase
{
    protected $notificationService;

    protected function setUp()
    {
        parent::setUp();
        $this->notificationService = self::$container->get(NotificationService::class);
    }

    public function testSendMail()
    {
        $entities = $this->load('tests/.fixtures/simpleUser.yml');

        $result = $this->notificationService->sendEmail([$entities['user_1']], 'subject', 'message');

        $this->assertCount(2, $result);
        $this->assertEquals('Email envoyé !', $result['msg']);
        $this->assertCount(1, $result['to']);
    }
}