<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Integration\WebApp\Season;

use App\Entity\WebApp\Season;
use App\Entity\WebApp\Tag;
use App\Repository\WebApp\Season\Doctrine\SeasonRepository;
use App\Repository\WebApp\Tag\Doctrine\TagRepository;
use App\Service\WebApp\Season\Exceptions\SeasonInvalidDataException;
use App\Service\WebApp\Season\SeasonService;
use App\Service\WebApp\Tag\Exceptions\TagInvalidDataException;
use App\Service\WebApp\Tag\TagService;
use App\Tests\TestCase;

/**
 * @group integration
 */
class TagServiceTest extends TestCase
{
    protected $tagService;

    protected $tagRepository;

    protected function setUp()
    {
        parent::setUp();
        $this->tagService = self::$container->get(TagService::class);
        $this->tagRepository = self::$container->get(TagRepository::class);
    }

    public function testRemoveTag()
    {
        $fixtures = $this->loadFile('tests/.fixtures/simpleTag.yml');

        $result = $this->tagService->removeTag($fixtures['tag_1']);

        $this->assertTrue($result);
        $this->assertCount(1, $this->tagRepository->findAll());
    }

    public function testCreateTagWithBadTitle()
    {
        $tag = [
            'title' => '',
            'type' => 'type',
        ];

        $this->expectException(TagInvalidDataException::class);
        $this->tagService->createTag($tag);
    }

    public function testCreateTag()
    {
        $tag = [
            'title' => 'tag',
            'type' => 'type',
        ];

        $result = $this->tagService->createTag($tag);

        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals('tag', $result->getTitle());
        $this->assertEquals('type', $result->getType());
    }

    public function testUpdateTag()
    {
        $fixtures = $this->loadFile('tests/.fixtures/simpleTag.yml');

        $tag = [
            'title' => 'tagupdate',
            'type' => 'type',
        ];

        $result = $this->tagService->updateTag($tag, $fixtures['tag_1']);

        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals('tagupdate', $result->getTitle());
        $this->assertNotNull($this->tagRepository->findByName('tagupdate'));
    }

    public function testUpdateSeasonWithBadTitle()
    {
        $fixtures = $this->loadFile('tests/.fixtures/simpleTag.yml');

        $tag = [
            'title' => '',
            'type' => 'type',
        ];

        $this->expectException(TagInvalidDataException::class);
        $this->tagService->updateTag($tag, $fixtures['tag_1']);
    }

    public function testGetLastAction()
    {
        $this->loadFile('tests/.fixtures/simpleTag.yml');

        $result = $this->tagService->getLastAction();

        $this->assertCount(2, $result);
        $this->assertCount(5, $result[0]);
        $this->assertCount(5, $result[1]);
    }
}