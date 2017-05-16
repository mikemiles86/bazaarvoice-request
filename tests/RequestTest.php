<?php

namespace BazaarvoiceRequest\Tests;

use BazaarvoiceRequest\BazaarvoiceRequest;
use Doctrine\Instantiator\Exception\InvalidArgumentException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;

class RequestTest extends \PHPUnit_Framework_TestCase {

  private function mockClient($data = '', $response_code = 200) {
    // Build mock objects.
    $stream = $this->getMockBuilder('GuzzleHttp\Psr7\Stream')
      ->disableOriginalConstructor()
      ->disableOriginalClone()
      ->disableArgumentCloning()
      ->disallowMockingUnknownTypes()
      ->getMock();
    $stream->expects($this->once())
      ->method('read')
      ->will($this->returnValue($data));

    $response = $this->getMockBuilder('GuzzleHttp\Psr7\Response')
      ->disableOriginalConstructor()
      ->disableOriginalClone()
      ->disableArgumentCloning()
      ->disallowMockingUnknownTypes()
      ->getMock();
    $response->expects($this->once())
      ->method('getStatusCode')
      ->will($this->returnValue($response_code));
    $response->expects($this->once())
      ->method('getBody')
      ->will($this->returnValue($stream));

    $client = $this->getMockBuilder('GuzzleHttp\Client')
      ->disableOriginalConstructor()
      ->disableOriginalClone()
      ->disableArgumentCloning()
      ->disallowMockingUnknownTypes()
      ->getMock();
    $client->expects($this->once())
      ->method('request')
      ->will($this->returnValue($response));

    return $client;

  }

  public function testReturnsNull() {
    $data = [
      'message' => 'Error Message',
      'code' => 999,
    ];
    $client = $this->mockClient(json_encode($data), 500);
    $request = new BazaarvoiceRequest($client, '1234abc');
    $this->assertNull($request->apiRequest('some/endpoint'));
  }

  public function testReturnsErrors() {
    $data = [
      'HasErrors' => TRUE,
      'Errors' => ['Invalid Request', 'Second Error'],
    ];
    $client = $this->mockClient(json_encode($data), 200);
    $request = new BazaarvoiceRequest($client, '1234abc');
    $request->apiRequest('some/endpoint');
    $this->assertNotEmpty($request->getRequestErrors());
  }

  public function testReturnsNoErrors() {
    $data = [
      'content' => 'hello world',
    ];
    $client = $this->mockClient(json_encode($data), 200);
    $request = new BazaarvoiceRequest($client, '1234ABC');
    $request->apiRequest('some/endpoint');
    $this->assertEmpty($request->getRequestErrors());
  }

  public function testReturnsJSON() {
    $data = [
      'content' => 'hello world'
    ];
    $client = $this->mockClient(json_encode($data), 200);
    $request = new BazaarvoiceRequest($client, '1234ABC');
    $response = $request->apiRequest('some/endpoint');
    $this->assertSame($data, $response);
  }
}