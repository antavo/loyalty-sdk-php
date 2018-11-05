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
            new \Antavo\LoyaltySdk\RestClient('st2', 'key', 'secret')
        );
    }

    /**
     * @covers \Antavo\LoyaltySdk\RestClient::__construct()
     */
    public function testConstructor() {
        $this->assertSame(
            'https://api.st2.antavo.com',
            (new Antavo\LoyaltySdk\RestClient('st2', 'key', 'secret'))->getBaseUrl()
        );
    }

    /**
     * @covers \Antavo\LoyaltySdk\RestClient::getCredentialScope()
     */
    public function testGetCredentialScope() {
        $this->assertSame(
            'st2/api/antavo_request',
            (new Antavo\LoyaltySdk\RestClient('st2', 'key', 'secret'))
                ->getCredentialScope()
        );
    }
}
