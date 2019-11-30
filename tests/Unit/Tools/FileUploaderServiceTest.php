<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Unit\Tools;

use App\Service\Tools\FileUploaderService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @group unit
 */
class FileUploaderServiceTest extends KernelTestCase
{
    public function testUpload()
    {
        $FileUploaderService = $this->getContainer()->get(FileUploaderService::class);

        $photo = new UploadedFile('public/images/static/logo.png', 'logo.png', 'image/png');

        $result = $FileUploaderService->upload($photo);

        $this->assertCount(2, $result);
        $this->assertFalse($result['status']);
        $this->assertNull($result['filename']);
    }

    private function getContainer()
    {
        self::bootKernel();
        $container = self::$container;

        return $container;
    }
}