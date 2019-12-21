<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Integration\WebApp\User;

use App\Entity\WebApp\Comment;
use App\Entity\WebApp\User;
use App\Repository\WebApp\Comment\Doctrine\CommentRepository;
use App\Repository\WebApp\User\Doctrine\UserRepository;
use App\Service\WebApp\Comment\CommentService;
use App\Service\WebApp\Comment\Exceptions\CommentInvalidDataException;
use App\Service\WebApp\User\Exceptions\UserInvalidDataException;
use App\Service\WebApp\User\UserService;
use App\Tests\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @group integration
 */
class UserServiceTest extends TestCase
{
    protected $userService;

    protected $userRepository;

    protected $encodedPassword;

    protected function setUp()
    {
        parent::setUp();
        $this->userService = self::$container->get(UserService::class);
        $this->userRepository = self::$container->get(UserRepository::class);
        $this->encodedPassword = self::$container->get(UserPasswordEncoderInterface::class);
    }

    public function testUpdateProfileWithBadToken()
    {
        $fixtures = $this->loadFile('tests/.fixtures/simpleUser.yml');

        $data = [
            'email' => 'test@test.com',
            'lastname' => 'damiens',
            'firstname' => 'florent',
            'token' => 'badToken'
        ];

        $this->expectException(UserInvalidDataException::class);
        $this->userService->updateProfile($data, $fixtures['user_1']);
    }

    public function testUpdateProfile()
    {
        $fixtures = $this->loadFile('tests/.fixtures/simpleUser.yml');

        $data = [
            'email' => 'test@test.com',
            'lastname' => 'damiens',
            'firstname' => 'florent',
            'token' => $this->getCsrfToken()->getToken('update-user')->getValue()
        ];

        $result = $this->userService->updateProfile($data, $fixtures['user_1']);

        $this->assertEquals('test@test.com', $result->getEmail());
        $this->assertEquals('damiens', $result->getLastname());
        $this->assertEquals('florent', $result->getFirstname());
    }

    public function testUpdateProfileWithEmailUsed()
    {
        $fixtures = $this->loadFile('tests/.fixtures/simpleUser.yml');

        $data = [
            'email' => 'admin@orange.fr',
            'lastname' => 'damiens',
            'firstname' => 'florent',
            'token' => $this->getCsrfToken()->getToken('update-user')->getValue()
        ];

        $result = $this->userService->updateProfile($data, $fixtures['user_1']);

        $this->assertEquals('damiens.florent@orange.fr', $result->getEmail());
        $this->assertEquals('damiens', $result->getLastname());
        $this->assertEquals('florent', $result->getFirstname());
    }

    public function testUpdateUserWithBadToken()
    {
        $fixtures = $this->loadFile('tests/.fixtures/simpleUser.yml');

        $data = [
            'email' => 'admin@orange.fr',
            'lastname' => 'damiens',
            'firstname' => 'florent',
            'roles' => ['ROLE_USER'],
            'created' => '2001-03-10 17:16:18',
            'token' => 'badToken'
        ];

        $this->expectException(UserInvalidDataException::class);
        $this->userService->updateUser($data, $fixtures['user_1']);
    }

    public function testUpdateUser()
    {
        $fixtures = $this->loadFile('tests/.fixtures/simpleUser.yml');

        $data = [
            'email' => 'adminTest@orange.fr',
            'lastname' => 'damiens',
            'firstname' => 'florent',
            'roles' => ['ROLE_USER', 'ROLE_AUTHOR'],
            'created' => '2001-03-10 17:16:18',
            'token' => $this->getCsrfToken()->getToken('update-user')->getValue()
        ];

        $result = $this->userService->updateUser($data, $fixtures['user_1']);

        $this->assertEquals('adminTest@orange.fr', $result->getEmail());
        $this->assertEquals('damiens', $result->getLastname());
        $this->assertEquals('florent', $result->getFirstname());
        $this->assertCount(2, $result->getRoles());
        $this->assertEquals('2001-03-10 17:16:18', $result->getCreated()->format('Y-m-d H:i:s'));
    }

    public function testUpdateUserWithBadName()
    {
        $fixtures = $this->loadFile('tests/.fixtures/simpleUser.yml');

        $data = [
            'email' => 'adminTest@orange.fr',
            'lastname' => '',
            'firstname' => 'florent',
            'roles' => ['ROLE_USER', 'ROLE_AUTHOR'],
            'created' => '2001-03-10 17:16:18',
            'token' => $this->getCsrfToken()->getToken('update-user')->getValue()
        ];

        $this->expectException(UserInvalidDataException::class);
        $this->userService->updateUser($data, $fixtures['user_1']);
    }

    public function testUpdatePasswordFailed()
    {
        $fixtures = $this->loadFile('tests/.fixtures/simpleUser.yml');

        $data = [
            'password_first' => 'first',
            'password_second' => 'first',
            'token' => $this->getCsrfToken()->getToken('update-password')->getValue()
        ];

        $this->expectException(UserInvalidDataException::class);
        $this->userService->updatePassword($data, $fixtures['user_1']);
    }

    public function testUpdatePassword()
    {
        $fixtures = $this->loadFile('tests/.fixtures/simpleUser.yml');

        $data = [
            'password_first' => 'first125*/',
            'password_second' => 'first125*/',
            'token' => $this->getCsrfToken()->getToken('update-password')->getValue()
        ];

        $result = $this->userService->updatePassword($data, $fixtures['user_1']);
        $this->assertInstanceOf(User::class, $result);
    }

    public function testRemoveUser()
    {
        $fixtures = $this->loadFile('tests/.fixtures/simpleUser.yml');

        $result = $this->userService->removeUser($fixtures['user_1']);

        $this->assertTrue($result);
        $this->assertCount(1, $this->userRepository->findAll());
    }

    public function testCreateUser()
    {
        $data = [
            'email' => 'adminTest@orange.fr',
            'lastname' => 'damiens',
            'firstname' => 'florent',
            'roles' => ['ROLE_USER', 'ROLE_AUTHOR'],
            'password_first' => 'first125*/',
            'password_second' => 'first125*/',
            'token' => $this->getCsrfToken()->getToken('create-user')->getValue()
        ];

        $result = $this->userService->createUser($data);

        $this->assertEquals('adminTest@orange.fr', $result->getEmail());
        $this->assertEquals('damiens', $result->getLastname());
        $this->assertEquals('florent', $result->getFirstname());
        $this->assertCount(2, $result->getRoles());
    }

    public function testGetLastAction()
    {
        $this->loadFile('tests/.fixtures/simpleUser.yml');

        $result = $this->userService->getLastAction();

        $this->assertCount(2, $result);
    }
}