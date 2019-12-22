<?php

use Behat\Behat\Context\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpClient\HttpClient;
use Webmozart\Assert\Assert;
use Symfony\Component\DependencyInjection\Container;

class FeatureContext implements Context
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Response|null
     */
    private $response;

    public function __construct(
        KernelInterface $kernel,
        Container $container
    )
    {
        $this->kernel = $kernel;
        $this->container = $container;
    }

    /**
     * @Given I load the fixture :fixtureName
     */
    public function iLoadTheFixture($fixtureName)
    {
        $loader = $this->container->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtureFile = sprintf('tests/.fixtures/%s.yml', $fixtureName);

        $loader->load([$fixtureFile]);
    }

    /**
     * @When I request the path :path with the method :method
     */
    public function iRequestTheUrlWithTheMethod(string $path, string $method)
    {
        $this->response = $this->kernel->handle(Request::create($path, $method));
    }

    /**
     * @Then the status code should be :code
     */
    public function theStatusCodeShouldBe(string $code)
    {
        Assert::eq($this->response->getStatusCode(), $code);
    }

    /**
     * @Then the content type should be :contentType
     */
    public function theContentTypeShouldBe(string $contentType)
    {
        Assert::contains($this->response->headers->get('Content-Type'), $contentType);
    }
}
