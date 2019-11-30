<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 18:28
 */

namespace App\Tests\Unit\Tools\Error\Factory;

use App\Entity\Error\GenericError;
use App\Service\Tools\Error\Factory\ErrorFactory;
use App\Service\WebApp\Comment\Exceptions\CommentInvalidDataException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group unit
 */
class ErrorFactoryTest extends KernelTestCase
{
    public function testCreate()
    {
         $ErrorFactory = $this->getContainer()->get(ErrorFactory::class);

         $result = $ErrorFactory->create(new CommentInvalidDataException([], 'erreur'));

         $this->assertInstanceOf(GenericError::class, $result);

         $this->assertEquals('erreur', $result->getMessage());
         $this->assertEquals('CommentInvalidDataException', $result->getType());
         $this->assertCount(0, $result->getContext());
    }

    private function getContainer()
    {
        self::bootKernel();
        $container = self::$container;

        return $container;
    }
}