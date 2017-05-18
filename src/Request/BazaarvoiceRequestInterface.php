<?php

namespace BazaarvoiceRequest\Request;

/**
 * Interface BazaarvoiceRequestInterface
 * @package BazaarvoiceRequest
 */
interface BazaarvoiceRequestInterface {

  /**
   * Makes an API request to Bazaarvoice.
   *
   * @param string $endpoint
   *   API endpoint to call.
   *
   * @param array  $configuration
   *   Key => value Array of configuration settings for API request.
   *   Expected keys:
   *     - method: Request method (GET, POST, etc..)
   *     - arguments: URL key/value arguments
   *     - options: Additional request options as defined at http://docs.guzzlephp.org/en/latest/request-options.html
   *
   * @param string $response_type
   *  String name of class to load as response object.
   *  Must be a subclass of BazaarvoiceRequest\Response\BazaarvoiceResponseBase;
   *
   * @return mixed
   *   Returned data from API call.
   */
  public function apiRequest($endpoint, array $configuration = [], $response_type = 'BazaarvoiceRequest\\Response\\BazaarvoiceResponse');

}