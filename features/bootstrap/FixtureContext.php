<?php

use Behat\Behat\Context\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpClient\HttpClient;
use Webmozart\Assert\Assert;
use App\Entity\WebApp\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Behat\Gherkin\Node\PyStringNode;

class FixtureContext implements Context
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(
        KernelInterface $kernel
    )
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given I load the fixture :fixtureName
     */
    public function iLoadTheFixture($fixtureName)
    {
        $loader = $this->kernel->getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtureFile = sprintf('tests/.fixtures/%s.yml', $fixtureName);

        imagejpeg(imagecreatetruecolor(100, 100), 'tests/.fixtures/images/uploads/test_photovergnat_1.jpeg');
        imagejpeg(imagecreatetruecolor(100, 100), 'tests/.fixtures/images/uploads/test_photovergnat_2.jpeg');

        $loader->load([$fixtureFile]);
    }

    /**
     * @Then Object :object in namespace :namespace with attribute :attribute equal :value should exist in database
     */
    public function objectInNamespaceWithAttributeEqualShouldExistInDatabase($object, $namespace, $attribute, $value)
    {
        $entity = $this->kernel->getContainer()->get('doctrine')
            ->getRepository("App\Entity\\$namespace\\$object")
            ->findOneBy(
                [
                    $attribute => $value
                ]
            );

        Assert::notNull($entity);
    }

    /**
     * @Then Object :object in namespace :namespace with attribute :attribute equal :value shouldn't exist in database
     */
    public function objectInNamespaceWithAttributeEqualShouldntExistInDatabase($object, $namespace, $attribute, $value)
    {
        $entity = $this->kernel->getContainer()->get('doctrine')
            ->getRepository("App\Entity\\$namespace\\$object")
            ->findOneBy(
                [
                    $attribute => $value
                ]
            );

        Assert::null($entity);
    }
}
