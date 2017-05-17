<?php

namespace BazaarvoiceRequest;

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
   * @return mixed
   *   Returned data from API call.
   */
  public function apiRequest($endpoint, array $configuration = []);

  /**
   * Return errors from a given API request.
   *
   * @return mixed
   */
  public function getRequestErrors();
}