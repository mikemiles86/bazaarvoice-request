# bazaarvoice-request
PHP library for making requests to Bazaarvoice APIs

# Bazaarvoice Request Library

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mikemiles86/bazaarvoice-request.svg?style=flat-square)](https://packagist.org/packages/mikemiles86/bazaarvoice-request)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/mikemiles86/bazaarvoice-request.svg?style=flat-square)](https://packagist.org/packages/mikemiles86/bazaarvoice-request)

PHP library for making requests to [Bazaarvoice APIs](https://developer.bazaarvoice.com/apis) and handling responses.

## Install

Via Composer

``` bash
$ composer require mikemiles86/bazaarvoice-request
```

## Usage

### Creating a Request
``` php
$client = new \GuzzleHttp\Client();
$api_key = '12345abcd';
$bazaarvoice_request = new \BazaarvoiceRequest\BazaarvoiceRequest($client, $api_key);

```
### Making a request
``` php

$client = new \GuzzleHttp\Client();
$api_key = '12345abcd';
$bazaarvoice_request = new \BazaarvoiceRequest\BazaarvoiceRequest($client, $api_key);

$reviews = $bazaarvoice_request->apiRequest('data/reviews');
```

#### Making a staging request
``` php

$client = new \GuzzleHttp\Client();
$api_key = '12345abcd';
$bazaarvoice_request = new \BazaarvoiceRequest\BazaarvoiceRequest($client, $api_key);

$reviews = $bazaarvoice_request->useStage()->apiRequest('data/reviews');
```

#### Making a request with additional configurations
``` php
$client = new \GuzzleHttp\Client();
$api_key = '12345abcd';
$bazaarvoice_request = new \BazaarvoiceRequest\BazaarvoiceRequest($client, $api_key);

$configuration = [
  'method' => 'POST',
  'options' => [
    'headers' => [
      'X-Forwarded-For' => '127.0.0.1',
    ],
  ],
];

$reviews = $bazaarvoice_request->apiRequest('data/reviews', $configuration);
```

``` php
$client = new \GuzzleHttp\Client();
$api_key = '12345abcd';
$bazaarvoice_request = new \BazaarvoiceRequest\BazaarvoiceRequest($client, $api_key);

$configuration = [
  'arguments' => [
    'ProductId' => 'my_product_123',
];

$product_reviews = $bazaarvoice_request->->apiRequest('data/reviews', $configuration);
```

### Get values from a response
``` php

$client = new \GuzzleHttp\Client();
$api_key = '12345abcd';
$bazaarvoice_request = new \BazaarvoiceRequest\BazaarvoiceRequest($client, $api_key);

$response = $bazaarvoice_request->useStage()->apiRequest('data/reviews');

$reviews = $response->getResponse('Results');

```

#### Get errors from a response

``` php

$client = new \GuzzleHttp\Client();
$api_key = '12345abcd';
$bazaarvoice_request = new \BazaarvoiceRequest\BazaarvoiceRequest($client, $api_key);

$response = $bazaarvoice_request->useStage()->apiRequest('data/reviews');

if ($response->getStatusCode() != '200' || $response->hasErrors()) {
  $errors = $response->getErrors();
}

```


## Testing

``` bash
$ composer test
```

## Credits

- [Mike Miles](https://github.com/mikemiles86)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
