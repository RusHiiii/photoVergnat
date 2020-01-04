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
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @When I make an XmlHttpRequest to path :path with the method :method and with the following payload and file attached
     */
    public function iXmlRequestTheUrlWithPayloadAndFileAttached(string $path, string $method, PyStringNode $payload = null)
    {
        $data =  $payload ? json_decode($payload->getRaw(), true) : null;

        $image = new UploadedFile($this->loadImageTemporary(),'image.jpeg','image/jpeg');

        $this->client->xmlHttpRequest(
            $method,
            $path,
            $data,
            [
                'file' => $image
            ]
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
     * @Then the content should have the following content
     */
    public function theContentShouldBe(PyStringNode $payload = null)
    {
        $dataContent = json_decode($payload->getRaw(), true);

        Assert::eq($dataContent, json_decode($this->response->getContent(), true));
    }

    private function loadImageTemporary()
    {
        $file = tempnam(sys_get_temp_dir(), 'test');
        imagejpeg(imagecreatetruecolor(100, 100), $file);

        return $file;
    }
}

