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
use App\Tests\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group unit
 */
class DataValidatorServiceTest extends TestCase
{
    protected $dataValidatorService;

    protected function setUp()
    {
        parent::setUp();
        $this->dataValidatorService = self::$container->get(DataValidatorService::class);
    }

    public function testValidateEmailWithGoodMail()
    {
        $result = $this->dataValidatorService->validateEmail('damiens.florent@orange.fr', 'Email');

        $this->assertTrue($result);
    }

    public function testValidateEmailWithBadMail()
    {
        $result = $this->dataValidatorService->validateEmail('damiens.florentorange.fr', 'Email');

        $this->assertFalse($result);

        $errors = $this->dataValidatorService->getErrors();

        $this->assertCount(1, $errors);
        $this->assertEquals('Le champs « Email » est invalide', $errors[0]);
    }

    public function testValidateNotBlank()
    {
        $result = $this->dataValidatorService->validateNotBlank('test', 'Message');

        $this->assertTrue($result);
    }

    public function testValidateNotNull()
    {
        $result = $this->dataValidatorService->validateNotNull(null, 'Tags');

        $this->assertFalse($result);

        $errors = $this->dataValidatorService->getErrors();

        $this->assertCount(1, $errors);
        $this->assertEquals('Le champs « Tags » est obligatoire', $errors[0]);
    }

    public function testValidateEqualTo()
    {
        $result = $this->dataValidatorService->validateEqualTo('password1', 'password2', 'Mot de passe');

        $this->assertFalse($result);

        $errors = $this->dataValidatorService->getErrors();

        $this->assertCount(1, $errors);
        $this->assertEquals('Les champs « Mot de passe » ne sont pas identique', $errors[0]);
    }

    public function testValidateImage()
    {
        $result = $this->dataValidatorService->validateImage(null, 'Image');

        $this->assertTrue($result);
    }

    public function testValidateRegex()
    {
        $result = $this->dataValidatorService->validateRegex('testregex', 'Mot de passe');

        $this->assertFalse($result);

        $errors = $this->dataValidatorService->getErrors();

        $this->assertCount(1, $errors);
        $this->assertEquals('Le champs « Mot de passe » n\'a pas le bon pattern', $errors[0]);
    }

    public function testValidateExist()
    {
        $result = $this->dataValidatorService->validateExist([], 'key', 'Tags');

        $this->assertFalse($result);

        $errors = $this->dataValidatorService->getErrors();

        $this->assertCount(1, $errors);
        $this->assertEquals('Le champs « Tags » ne peux pas être vide', $errors[0]);
    }
}