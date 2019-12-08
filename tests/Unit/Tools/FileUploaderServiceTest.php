<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Unit\Tools;

use App\Service\Tools\FileUploaderService;
use App\Tests\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @group unit
 */
class FileUploaderServiceTest extends TestCase
{
    protected $fileUploaderService;

    protected $file;

    protected function setUp()
    {
        parent::setUp();
        $this->fileUploaderService = self::$container->get(FileUploaderService::class);
    }

    public function testUploadError()
    {
        $photo = new UploadedFile($this->loadImage(), 'logo.png', 'image/png');

        $result = $this->fileUploaderService->upload($photo);

        $this->assertCount(2, $result);
        $this->assertFalse($result['status']);
        $this->assertNull($result['filename']);
    }

    public function testUploadSuccess()
    {
        $photo = new UploadedFile($this->loadImage(), 'logo.png', 'image/png', null, true);

        $result = $this->fileUploaderService->upload($photo);

        $this->assertCount(2, $result);
        $this->assertTrue($result['status']);
        $this->assertNotNull($result['filename']);
    }
}