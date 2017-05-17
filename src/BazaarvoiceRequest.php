<?php

namespace BazaarvoiceRequest;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Class BazaarvoiceRequest
 * @package BazaarvoiceRequest
 */
class BazaarvoiceRequest implements BazaarvoiceRequestInterface {

  private static $api_version = '5.4';
  protected $client;
  protected $domain = 'api.bazaarvoice.com';
  protected $apiKey;
  protected $use_stage = FALSE;
  protected $errors = [];


  public function __construct(ClientInterface $client, $apiKey, $use_stage = FALSE) {
    $this->client = $client;
    $this->apiKey = $apiKey;
    $this->use_stage = ($use_stage === TRUE);
    $this->errors = [];
  }

  public function apiRequest($endpoint, array $configuration = []) {
    // Get request method, arguments and options
    list($method, $arguments, $options) = $this->splitConfiguration($configuration);
    // Get the URL for the request.
    $request_url = $this->buildUrl($endpoint, $arguments);
    // Build HTTP Request options.
    $method = ($method == 'POST' ? 'POST' : 'GET');
    $request_options = array(
      'timeout' => 60,
      'headers' => array(
        'Accept-Encoding' => 'gzip;q=0, deflate;q=0',
      ),
    );
    // Want to make a POST request?
    if ($method == 'POST') {
      // Need to split arguments from url and put in body data.
      list($request_url, $request_options['data']) = explode('?', $request_url);
      // Need to fake being a form because how http_request works.
      $request_options['headers'] = array('Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8');
    }

    // Additional request options?
    if (!empty($options)) {
      $request_options = array_merge_recursive($request_options, $options);
    }

    $response_data = NULL;
    // Attempt to get a response.
    try {
      // Make request and get response object.
      $response = $this->client->request($method, $request_url, $request_options);
      // Get the status code of the response.
      $status_code = $response->getStatusCode();
      // Get the body content.
      $response_body = $response->getBody();
      // Get the stream size.
      $stream_size = $response_body->getSize();
      // Attempt to json decode the content.
      $response_data = json_decode($response_body->read($stream_size), TRUE);
      // Returned status code not 200?
      if ($status_code < 200 || $status_code > 299) {
        // Set error.
        $response_data['HasErrors'] = TRUE;
        $response_data['Errors'][$response_data['code']] = $response_data['message'];
      }
    }
    catch (RequestException $e) {
      //@TODO: what to do with this exception?
    }

    // Were there errors?
    if (isset($response_data['HasErrors']) && $response_data['HasErrors']) {
      // Form errors?
      if (!empty($response_data['FormErrors'])) {
        $this->errors = $response_data['FormErrors'];
      }
      // Else general errors?
      elseif (!empty($response_data['Errors'])) {
        $this->errors = $response_data['Errors'];
      }
      // Set data to NULL.
      $response_data = NULL;
    }

    return $response_data;
  }

  /**
   * Overrides getRequestErrors().
   */
  public function getRequestErrors() {
    return $this->errors;
  }

  private function buildUrl($endpoint, array $additional_parameters = []) {
    // Build base domain URI.
    $base = ($this->use_stage ? 'stg.' : '') . $this->domain;
    // Build initial array of parameters.
    $parameters = [
      'passKey=' . $this->apiKey,
      'ApiVersion=' . self::$api_version,
    ];

    // Adding additional parameters?
    if (!empty($additional_parameters)) {
      // Loop through each parameter to build out correctly.
      foreach ($additional_parameters as $param_name => $param_value) {
        // If value is an array, then passing multiple sub parameter values.
        if (is_array($param_value)) {
          // Build each sub parameter.
          foreach ($param_value as $sub_param_name => $sub_param_value) {
            // Add to parameters array.
            $parameters[] = $param_name . '=' . $sub_param_name . ':' . $sub_param_value;
          }
        }
        // Else it is just a single value parameter.
        else {
          // Add to parameters array.
          $parameters[] = $param_name . '=' . $param_value;
        }
      }
    }

    // Implode all of the parameters.
    $parameters = implode('&', $parameters);

    // return the built url.
    return $base . '/' . $endpoint . '.json?' . $parameters;
  }

  private function splitConfiguration(array $options) {
    $return_array = [
      'method' => 'GET',
      'arguments' => [],
      'options' => [],
    ];

    // Request method passed?
    if (isset($options['method'])) {
      $return_array['method'] = $options['method'];
    }
    // URL arguments passed?
    if (isset($options['arguments'])) {
      $return_array['arguments'] = $options['arguments'];
    };

    // Request options passed?
    if (isset($options['options'])) {
      $return_array['options'] = $options['options'];
    }

    return array_values($return_array);
  }

}