<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Integration\WebApp\Comment;

use App\Entity\WebApp\Comment;
use App\Entity\WebApp\Season;
use App\Repository\WebApp\Comment\Doctrine\CommentRepository;
use App\Repository\WebApp\Season\Doctrine\SeasonRepository;
use App\Service\WebApp\Comment\CommentService;
use App\Service\WebApp\Comment\Exceptions\CommentInvalidDataException;
use App\Service\WebApp\Season\Exceptions\SeasonInvalidDataException;
use App\Service\WebApp\Season\SeasonService;
use App\Tests\TestCase;

/**
 * @group integration
 */
class CommentServiceTest extends TestCase
{
    protected $commentService;

    protected $commentRepository;

    protected function setUp()
    {
        parent::setUp();
        $this->commentService = self::$container->get(CommentService::class);
        $this->commentRepository = self::$container->get(CommentRepository::class);
    }

    public function testRemoveComment()
    {
        $fixtures = $this->load('tests/.fixtures/completeCategory.yml');

        $result = $this->commentService->removeComment($fixtures['comment_1']);

        $this->assertTrue($result);
        $this->assertCount(0, $this->commentRepository->findAll());
    }

    public function testCreateCommentWithBadToken()
    {
        $data = [
            'name' => 'RusHii',
            'message' => 'frrefrefre',
            'email' => 'contact@gmail.com',
            'category' => '1',
            'token' => 'badToken'
        ];

        $this->expectException(CommentInvalidDataException::class);
        $this->commentService->createComment($data);
    }

    public function testCreateCommentWithBadData()
    {
        $data = [
            'name' => 'RusHii',
            'message' => '',
            'email' => 'contact@gmail.com',
            'category' => '1',
            'token' => $this->getCsrfToken()->getToken('create-comment')->getValue()
        ];

        $this->expectException(CommentInvalidDataException::class);
        $this->commentService->createComment($data);
    }

    public function testCreateCommenta()
    {
        $data = [
            'name' => 'RusHii',
            'message' => 'frrefrefre',
            'email' => 'contact@gmail.com',
            'category' => '1',
            'token' => $this->getCsrfToken()->getToken('create-comment')->getValue()
        ];

        $result = $this->commentService->createComment($data);

        $this->assertInstanceOf(Comment::class, $result);
        $this->assertEquals('RusHii', $result->getName());
        $this->assertEquals('frrefrefre', $result->getMessage());
        $this->assertEquals('contact@gmail.com', $result->getEmail());
    }

    public function testUpdateComment()
    {
        $fixtures = $this->load('tests/.fixtures/completeCategory.yml');

        $data = [
            'name' => 'edit',
            'message' => 'frrefrefre',
            'email' => 'contact@gmail.com',
            'category' => '1',
            'token' => $this->getCsrfToken()->getToken('update-comment')->getValue()
        ];

        $result = $this->commentService->updateComment($data, $fixtures['comment_1']);

        $this->assertInstanceOf(Comment::class, $result);
        $this->assertEquals('edit', $result->getName());
        $this->assertNotNull($this->commentRepository->find(1));
    }

    public function testUpdateCommentWithBadData()
    {
        $fixtures = $this->load('tests/.fixtures/completeCategory.yml');

        $data = [
            'name' => '',
            'message' => 'frrefrefre',
            'email' => 'contact@gmail.com',
            'category' => '1',
            'token' => $this->getCsrfToken()->getToken('update-comment')->getValue()
        ];

        $this->expectException(CommentInvalidDataException::class);
        $this->commentService->updateComment($data, $fixtures['comment_1']);
    }
}