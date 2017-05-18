<?php

namespace BazaarvoiceRequest\Tests;

use BazaarvoiceRequest\Response\BazaarvoiceResponse;

class ResponseTest extends \PHPUnit_Framework_TestCase {

  public function testHasErrors() {

    $data = [
      'Errors' => ['Error One', 'Error Two'],
    ];

    $response = new BazaarvoiceResponse('GET', 200, 'http://www.example.com', [], $data);
    $this->assertTrue($response->hasErrors());

    $data = [
      'FormErrors' => ['Form Error One', 'Form Error Two'],
    ];

    $response = new BazaarvoiceResponse('GET', 200, 'http://www.example.com', [], $data);
    $this->assertTrue($response->hasErrors());
  }

  public function testDoesNotHaveErrors() {
    $data = ['content' => 'Hello world'];
    $response = new BazaarvoiceResponse('GET', 200, 'http://www.example.com', [], $data);
    $this->assertFalse($response->hasErrors());
  }

  public function testRetrieveConfiguration() {
    $configuration = ['method' => 'GET'];
    $response = new BazaarvoiceResponse('GET', 200, 'http://www.example.com', $configuration);
    $this->assertSame($configuration, $response->getConfiguration());
    $this->assertSame($configuration['method'], $response->getConfiguration('method'));
  }

  public function testRetrieveResponseData() {
    $data = ['content' => 'Hello world'];
    $response = new BazaarvoiceResponse('GET', 200, 'http://www.example.com', [], $data);
    $this->assertSame($data, $response->getResponse());
    $this->assertSame($data['content'], $response->getResponse('content'));
  }

}