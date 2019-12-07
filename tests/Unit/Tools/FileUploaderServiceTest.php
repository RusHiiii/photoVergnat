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

    protected function setUp()
    {
        parent::setUp();
        $this->fileUploaderService = self::$container->get(FileUploaderService::class);
    }

    public function testUpload()
    {
        $photo = new UploadedFile('public/images/static/logo.png', 'logo.png', 'image/png');

        $result = $this->fileUploaderService->upload($photo);

        $this->assertCount(2, $result);
        $this->assertFalse($result['status']);
        $this->assertNull($result['filename']);
    }
}