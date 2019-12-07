<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Integration\WebApp\Information;

use App\Entity\WebApp\Season;
use App\Repository\WebApp\Season\Doctrine\SeasonRepository;
use App\Repository\WebApp\User\Doctrine\UserRepository;
use App\Service\Tools\NotificationService;
use App\Service\WebApp\Information\InformationService;
use App\Service\WebApp\Information\Validator\InformationValidator;
use App\Service\WebApp\Season\Exceptions\SeasonInvalidDataException;
use App\Service\WebApp\Season\SeasonService;
use App\Tests\TestCase;
use Twig\Environment;

/**
 * @group integration
 */
class InformationServiceTest extends TestCase
{
    protected $informationValidator;

    protected $notificationService;

    protected $userRepository;

    protected function setUp()
    {
        parent::setUp();
        $this->informationValidator = self::$container->get(InformationValidator::class);
        $this->notificationService = self::$container->get(NotificationService::class);
        $this->userRepository = self::$container->get(UserRepository::class);
    }

    public function testSendContactMail()
    {
        $this->load('tests/.fixtures/simpleUser.yml');

        $templating = $this->createMock(Environment::class);
        $templating->expects($this->any())->method('render')->willReturn(true);

        $informationService = new InformationService($this->informationValidator, $this->notificationService, $this->userRepository, $templating);

        $data = [
            'subject' => 'test',
            'choice' => '1',
            'message' => 'message',
            'email' => 'contact@gmail.com',
            'token' => $this->getCsrfToken()->getToken('send-mail')->getValue()
        ];

        $result = $informationService->sendContactMail($data);

        $this->assertTrue($result);
    }
}