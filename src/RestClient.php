<?php
namespace Antavo\Loyalty\Sdk;

use Pakard\RestClient\CurlTransport;
use Pakard\RestClient\RequestInterface;

/**
 *
 */
class RestClient extends \Pakard\RestClient\RestClient {
    /**
     * @inheritdoc
     */
    protected $_baseUrl = 'https://api.%s.antavo.com';

    /**
     * @var string
     */
    protected $_service = 'api';

    /**
     * @var string
     */
    protected $_region;

    /**
     * @var string
     */
    protected $_key;

    /**
     * @var string
     */
    protected $_secret;

    /**
     * @param string $region
     * @param string $key
     * @param string $secret
     */
    public function __construct($region, $key, $secret) {
        $this->_region = $region;
        $this->_key = $key;
        $this->_secret = $secret;
        $this->_baseUrl = sprintf($this->_baseUrl, $region);
    }

    /**
     * @return string
     */
    public function getCredentialScope() {
        return sprintf(
            '%s/%s/antavo_request',
            $this->_region,
            $this->_service
        );
    }

    /**
     * @param \Pakard\RestClient\RequestInterface $request
     * @return $this
     */
    public function signRequest(RequestInterface $request) {
        $escher = \Escher::create($this->getCredentialScope())
            ->setVendorKey('Antavo')
            ->setAlgoPrefix('ANTAVO')
            ->setDateHeaderKey('Date')
            ->setAuthHeaderKey('Authorization');

        $headers = $escher->signRequest(
            $this->_key,
            $this->_secret,
            $request->getMethod(),
            $request->getUrl(),
            $request->encodeBody()
        );

        foreach ($headers as $name => $value) {
            $request->addHeader($name, $value);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function beforeSend() {
        if (!$this->getTransport()) {
            $this->setTransport(new CurlTransport);
        }

        $this->signRequest(
            $request = $this->getRequest()
                ->addHeader('Content-Type', 'application/json; charset="UTF-8"')
                ->addHeader('User-Agent', 'Antavo Loyalty PHP SDK Client 2.0')
        );
    }

    /**
     * @param string $customer
     * @param string $action
     * @param array $data
     * @return mixed
     */
    public function sendEvent($customer, $action, array $data = []) {
        return $this->send(
            RequestInterface::METHOD_POST,
            '/events',
            compact('customer', 'action', 'data')
        );
    }
}
