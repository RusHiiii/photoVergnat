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

class RequestContext implements Context
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Response|null
     */
    private $response;

    /**
     * @var KernelBrowser
     */
    private $client;

    public function __construct(
        KernelInterface $kernel,
        KernelBrowser $client
    )
    {
        $this->kernel = $kernel;
        $this->client = $client;
    }

    /**
     * @When I make an HttpRequest to path :path with the method :method
     */
    public function iRequestTheUrlWithTheMethod(string $path, string $method)
    {
        $this->client->request(
            $method,
            $path
        );
        $this->response = $this->client->getResponse();
    }

    /**
     * @When I make an XmlHttpRequest to path :path with the method :method and with the following payload
     */
    public function iXmlRequestTheUrlWithPayload(string $path, string $method, PyStringNode $payload = null)
    {
        $data =  $payload ? json_decode($payload->getRaw(), true) : null;

        $this->client->xmlHttpRequest(
            $method,
            $path,
            $data
        );
        $this->response = $this->client->getResponse();
    }

    /**
     * @When I make an XmlHttpRequest to path :path with the method :method
     */
    public function iXmlRequestTheUrl(string $path, string $method)
    {
        $this->client->xmlHttpRequest(
            $method,
            $path
        );
        $this->response = $this->client->getResponse();
    }

    /**
     * @Given I am logged with the user :email
     */
    public function iAmLoggedWithTheUser(string $email)
    {
        $session = $this->kernel->getContainer()->get('session');

        $user = $this->kernel->getContainer()->get('doctrine')
            ->getRepository(User::class)
            ->findByEmail($email);

        if ($user == null) {
            throw new \Exception();
        }

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $this->client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
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
