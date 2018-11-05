<?php
namespace Antavo\LoyaltySdk;

use Antavo\SignedToken\SignedToken;

/**
 * @method $this setToken(string $token)
 */
class CustomerToken extends SignedToken {
    /**
     * @var string
     */
    protected $_cookieName = '__alc';

    /**
     * @var string
     */
    protected $_cookieDomain;

    /**
     * @param string $domain
     * @return string
     * @static
     */
    public static function calculateBaseDomain($domain) {
        return implode(
            '.',
            array_slice(
                explode('.', $domain),
                preg_match('/co\.[a-z]{2}|com.au$/', $domain) ? -3 : -2
            )
        );
    }

    /**
     * @inheritdoc
     *
     * Sets base cookie domain from HTTP_HOST environment variable.
     */
    public function __construct($secret, $expires_at = NULL) {
        parent::__construct($secret, $expires_at);
        $this->_cookieDomain = static::calculateBaseDomain(getenv('HTTP_HOST'));
    }

    /**
     * @return string
     */
    public function getCookieName() {
        return $this->_cookieName;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setCookieName($name) {
        $this->_cookieName = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCookieDomain() {
        return $this->_cookieDomain;
    }

    /**
     * @param string $domain
     * @return $this
     */
    public function setCookieDomain($domain) {
        $this->_cookieDomain = $domain;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomer() {
        if (isset($this->payload['customer'])) {
            return $this->payload['customer'];
        }

        return NULL;
    }

    /**
     * @param int|string $customer
     * @return $this
     */
    public function setCustomer($customer) {
        $this->payload['customer'] = $customer;
        return $this;
    }

    /**
     * Returns expiration time for cookie based on token expiration setting.
     *
     * @return int
     */
    protected function getCookieExpirationTime() {
        if ($this->expires_at > 0) {
            return $this->getCalculatedExpirationTime();
        }

        return 0;
    }

    /**
     * Sets customer token cookie.
     *
     * @return bool
     * @see \Antavo\LoyaltySdk\CustomerToken::getCookieName()
     * @see \Antavo\LoyaltySdk\CustomerToken::setCookieName()
     */
    public function setCookie() {
        return setcookie(
            $this->getCookieName(),
            (string) $this,
            $this->getCalculatedExpirationTime(),
            '/',
            $this->getCookieDomain()
        );
    }

    /**
     * @return bool
     */
    public function unsetCookie() {
        return setcookie(
            $this->getCookieName(),
            '',
            time() - 3600,
            '/',
            $this->getCookieDomain()
        );
    }
}
