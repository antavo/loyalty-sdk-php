# Antavo Loyalty SDK for PHP

## Table of Contents

* [Requirements](#requirements)
* [Usage](#usage)
  * [Including the Library](#including-the-library)
  * [The REST Client](#the-rest-client)
  * [Customer Token](#customer-token)
  * [Validating Webhook Messages](#validating-webhook-messages)


## Requirements

* PHP 5.5
* PHP cURL extension

## Usage

### Including the Library

#### Using the Phar package

~~~php
require 'phar://antavo-loyalty-sdk.phar';

// Now you can use the classes inside the archive.
~~~


### The REST client

The Antavo API REST client is a small client to perform requests to the API.

It uses two other libraries:

* `Antavo\SignedToken` to handle web tokens.
* `Escher` is a library to sign HTTP requests, also to validate them. It generalizes the signature method used by AWS.
* `Pakard\RestClient` is a small, generic REST client.


#### Creating an instance

~~~php
$client = new Antavo\Loyalty\Sdk\RestClient('REGION', 'API KEY', 'API SECRET');
~~~

Where:

* `REGION` is part of the credential scope (used to sign the request), it is determined by the account;
* `API KEY` identifies the account itself;
* `API SECRET` used to sign the request.

API endpoint base URL is calculated using `REGION` (though it can be changed via the `setBaseUrl()` method).


#### Sending a request

The underlying client has a `send()` method with the following signature:

~~~php
public function send(string $method, string $url, mixed $data = NULL): mixed;
~~~

It makes possible to perform any kind of REST request:

~~~php
$response = $client->send('GET', '/customer/' . $customer->id);
~~~

`$response` will hold the parsed JSON response.

~~~php
$response = $client->send(
    'POST',
    '/events',
    [
        'customer' => $customer->id,
        'action' => 'profile',
        'data' => [
            'email' => $customer->email,
        ]
    ]
);
~~~


#### Shorthands

##### Sending an event

~~~php
// Assuming $customer is some kind of model object instance.
$client->sendEvent(
    $customer->id,
    'profile',
    [
        'email' => $customer->email,
        'custom_field' => get_custom_value(),
    ]
);
~~~

#### Error Handling

Note that the API may return a HTTP status code other than 2xx, in which case the client throws an exception.

Because of that it is strongly recommended to wrap all requests in try-catch:

~~~php
try {
    $result = $client->send('GET', '/customer');
} catch (\Pakard\RestClient\StatusCodeException $e) {
    // You can still retrieve the original (error) response:
    $result = $client->getResponse()->getBody();
}
~~~

There may occur other kind of exceptions, all descendants of `Pakard\RestClient\Exception`:

* `Pakard\RestClient\ResponseParserException` on malformed response body;
* `Pakard\RestClient\TransportException` on any error produced by the PHP cURL extension.


### Customer Token

`Antavo\Loyalty\Sdk\CustomerToken` is used to create & validate web tokens to authenticate the customer in the embedded loyalty hub.

#### Creating an instance

~~~php
// Initializing a new token with the secret and with expiration time.
$token = new Antavo\Loyalty\Sdk\CustomerToken('API SECRET', $expires_in);
~~~

* `API SECRET` used to attach a hash to the token, so later it can be validated.
* `$expires_in` is an integer value: the number of seconds the token considered valid. 0 means no expiration, values smaller than 30 days (in seconds) considered as time-to-live, anything bigger is taken as a Unix timestamp.

Upon instantiation the token sets itself a default cookie domain from the environment, that can be override via `setCookieDomain()`.


#### Creating a new token

It can be retrieved by setting a customer ID, then simply casting the token object to string:

~~~php
echo (string) (new Antavo\Loyalty\Sdk\CustomerToken('API SECRET', $expires_in))
    ->setCustomer($customer->id);
~~~


#### Customer token in cookie

Setting a customer token cookie:

~~~php
$token = (new Antavo\Loyalty\Sdk\CustomerToken('API SECRET', $expires_in))
    ->setCustomer($customer->id);

if (!$token->setCookie()) {
    // Couldn't set the cookie...
}
~~~

Then unsetting it:

~~~php
$token->unsetCookie();
~~~


#### Retrieving cookie value

~~~php
$token = new Antavo\Loyalty\Sdk\CustomerToken('API SECRET', $expires_in);

try {
    if (isset($_COOKIE[$token->getCookieName()]) {
        $token->setToken($_COOKIE[$token->getCookieName()]);
    }
} catch (Antavo\SignedToken\Exceptions\Exception $e) {
    // The token is either expired or invalid...
}
~~~


## Validating Webhook Messages

There are cases when you need to handle webhook messages sent by Antavo. You can also use the REST client to authenticate such requests:

~~~php
// Creating a REST client with valid region and credentials
// (though credentials won't be used for authentication -- see below).
$client = new Antavo\Loyalty\Sdk\RestClient('REGION', 'API KEY', 'API SECRET');

// Obtaining a configured Escher client from the REST client.
$escher = $client->createEscher();

// Authenticating current request using credential key-value pairs.
$escher->authenticate(
    [
        'API KEY' => 'API SECRET'
    ]
);
~~~

For further details see the [EscherPHP documentation page](https://github.com/emartech/escher-php#validating-a-request).
