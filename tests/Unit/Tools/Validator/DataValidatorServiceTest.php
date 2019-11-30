<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 30/11/2019
 * Time: 13:58
 */

namespace App\Tests\Unit\Tools\Validator;

use App\Service\Tools\ToolsService;
use App\Service\Tools\Validator\DataValidatorService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group unit
 */
class DataValidatorServiceTest extends KernelTestCase
{
    public function testValidateEmailWithGoodMail()
    {
        $DataValidatorService = $this->getContainer()->get(DataValidatorService::class);

        $result = $DataValidatorService->validateEmail('damiens.florent@orange.fr', 'Email');

        $this->assertTrue($result);
    }

    public function testValidateEmailWithBadMail()
    {
        $DataValidatorService = $this->getContainer()->get(DataValidatorService::class);

        $result = $DataValidatorService->validateEmail('damiens.florentorange.fr', 'Email');
        $this->assertFalse($result);

        $errors = $DataValidatorService->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Le champs « Email » est invalide', $errors[0]);
    }

    public function testValidateNotBlank()
    {
        $DataValidatorService = $this->getContainer()->get(DataValidatorService::class);

        $result = $DataValidatorService->validateNotBlank('test', 'Message');
        $this->assertTrue($result);
    }

    public function testValidateNotNull()
    {
        $DataValidatorService = $this->getContainer()->get(DataValidatorService::class);

        $result = $DataValidatorService->validateNotNull(null, 'Tags');
        $this->assertFalse($result);

        $errors = $DataValidatorService->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Le champs « Tags » est obligatoire', $errors[0]);
    }

    public function testValidateEqualTo()
    {
        $DataValidatorService = $this->getContainer()->get(DataValidatorService::class);

        $result = $DataValidatorService->validateEqualTo('password1', 'password2', 'Mot de passe');
        $this->assertFalse($result);

        $errors = $DataValidatorService->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Les champs « Mot de passe » ne sont pas identique', $errors[0]);
    }

    public function testValidateImage()
    {
        $DataValidatorService = $this->getContainer()->get(DataValidatorService::class);

        $result = $DataValidatorService->validateImage(null, 'Image');
        $this->assertTrue($result);
    }

    public function testValidateRegex()
    {
        $DataValidatorService = $this->getContainer()->get(DataValidatorService::class);

        $result = $DataValidatorService->validateRegex('testregex', 'Mot de passe');
        $this->assertFalse($result);

        $errors = $DataValidatorService->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Le champs « Mot de passe » n\'a pas le bon pattern', $errors[0]);
    }

    public function testCsrfToken()
    {
        $DataValidatorService = $this->getContainer()->get(DataValidatorService::class);

        $result = $DataValidatorService->validateCsrfToken('token', 'Token');
        $this->assertFalse($result);

        $errors = $DataValidatorService->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Token invalide', $errors[0]);
    }

    public function testValidateExist()
    {
        $DataValidatorService = $this->getContainer()->get(DataValidatorService::class);

        $result = $DataValidatorService->validateExist([], 'key', 'Tags');
        $this->assertFalse($result);

        $errors = $DataValidatorService->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Le champs « Tags » ne peux pas être vide', $errors[0]);
    }

    private function getContainer()
    {
        self::bootKernel();
        $container = self::$container;

        return $container;
    }
}