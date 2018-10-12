<?php
namespace Antavo\Sdk;

/**
 *
 */
class RestClient extends \Pakard\RestClient\RestClient {
    /**
     * @var string
     */
    protected $_key;

    /**
     * @var string
     */
    protected $_secret;

    /**
     * @param string $key
     * @param string $secret
     */
    public function __construct($key, $secret) {
        $this->_key = $key;
        $this->_secret = $secret;
    }
}
