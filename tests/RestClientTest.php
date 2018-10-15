<?php

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
            new \Antavo\Sdk\RestClient('st2', 'key', 'secret')
        );
    }

    /**
     * @covers \Antavo\Sdk\RestClient::__construct()
     */
    public function testConstructor() {
        $this->assertSame(
            'https://api.st2.antavo.com',
            (new Antavo\Sdk\RestClient('st2', 'key', 'secret'))->getBaseUrl()
        );
    }

    /**
     * @covers \Antavo\Sdk\RestClient::getCredentialScope()
     */
    public function testGetCredentialScope() {
        $this->assertSame(
            'st2/api/antavo_request',
            (new Antavo\Sdk\RestClient('st2', 'key', 'secret'))
                ->getCredentialScope()
        );
    }
}
