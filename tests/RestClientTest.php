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
            new \Antavo\Sdk\RestClient
        );
    }
}
