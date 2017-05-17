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
   * @param array  $parameters
   *   Array of parameters to pass to endpoint.
   *
   * @param string $method
   *   API method call. GET|POST
   *
   * @param array  $headers
   *   Array of additional headers to pass.
   *
   * @return mixed
   *   Returned data from API call.
   */
  public function apiRequest($endpoint, array $parameters = [], $method = 'GET', array $headers = []);

  /**
   * Return errors from a given API request.
   *
   * @return mixed
   */
  public function getRequestErrors();
}