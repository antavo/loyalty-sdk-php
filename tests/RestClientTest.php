<?php
use Antavo\Loyalty\Sdk\RestClient;
use Pakard\RestClient\RequestInterface;

/**
 *
 */
class RestClientTest extends PHPUnit\Framework\TestCase {
    /**
     * @coversNothing
     */
    public function testInheritance() {
        $this->assertInstanceOf(
            \Pakard\RestClient\RestClient::class,
            new RestClient('st2', 'key', 'secret')
        );
    }

    /**
     * @covers \Antavo\Loyalty\Sdk\RestClient::__construct()
     */
    public function testConstructor() {
        $this->assertSame(
            'https://api.st2.antavo.com',
            (new RestClient('st2', 'key', 'secret'))->getBaseUrl()
        );
    }

    /**
     * @covers \Antavo\Loyalty\Sdk\RestClient::getCredentialScope()
     */
    public function testGetCredentialScope() {
        $this->assertSame(
            'st2/api/antavo_request',
            (new RestClient('st2', 'key', 'secret'))
                ->getCredentialScope()
        );
    }

    /**
     * @covers \Antavo\Loyalty\Sdk\RestClient::sendEvent()
     */
    public function testSendEvent() {
        $client = $this->getMockBuilder(RestClient::class)
            ->setMethods(['send'])
            ->setConstructorArgs(['region', 'key', 'secret'])
            ->getMock();

        $client->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo(RequestInterface::METHOD_POST),
                $this->equalTo('/events'),
                $this->equalTo(
                    [
                        'customer' => 'customer1',
                        'action' => 'opt_in',
                        'data' => [],
                    ]
                )
            );

        /** @var RestClient $client */
        $client->sendEvent('customer1', 'opt_in');
    }
}
