<?php

namespace BazaarvoiceRequest\Request;

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


  public function __construct(ClientInterface $client, $apiKey) {
    $this->client = $client;
    $this->apiKey = $apiKey;
    $this->use_stage = ($use_stage === TRUE);
    $this->errors = [];
    return $this;
  }

  public function useStage() {
    $this->use_stage = TRUE;
    return $this;
  }

  public function useProduction() {
    $this->use_stage = FALSE;
    return $this;
  }

  public function apiRequest($endpoint, array $configuration = [], $response_type = 'BazaarvoiceRequest\\Response\\BazaarvoiceResponse') {
    // Get request method, arguments and options.
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

    $response_data = [];
    $status_code = 500;
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
      $data = json_decode($response_body->read($stream_size), TRUE);
      if (is_array($data)) {
        $response_data = $data;
      }
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

    // Build a response object.
    $response = $this->buildResponse($response_type, $method, $status_code, $request_url, $configuration, $response_data);
    return $response;
  }

  /**
   * Create Bazaarvoice URL with added parameters.
   *
   * @param string $endpoint
   *   API endpoint to call.
   *
   * @param array $additional_parameters
   *   Key/value of url parameters to add to URL.
   *
   * @return string
   *   Formatted API request URL.
   */
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

  /**
   * Splits API request options array into main buckets.
   *
   * @param array $options
   *   Key/Value array of api request options.
   *
   * @return array
   */
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

  /**
   * Returns a BazaarvoiceRequest\Response object.
   *
   * @param string $response_type
   *   Class name of response type to load.
   *
   * @param string $method
   *   HTTP request method.
   *
   * @param string $status_code
   *   HTTP request status code.
   *
   * @param string $request_url
   *   URL that request was made to.
   *
   * @param array $configuration
   *   Configuration settings that were used in request.
   *
   * @param array $response_data
   *   Raw data that was returned from response.
   *
   * @return bool|mixed
   *   FALSE or instance of response class.
   */
  private function buildResponse($response_type, $method, $status_code, $request_url, array $configuration = [], array $response_data = []) {
    $object = FALSE;
    // Check that a string was passed.
    if (is_string($response_type)) {
      // Check to see if this class exists.
      if (class_exists($response_type)) {
        // Check that this class extends the ContentTypeBase class.
        if (is_subclass_of($response_type, 'BazaarvoiceRequest\\Response\\BazaarvoiceResponseBase')) {
          // Instantiate object of this class.
          $object = new $response_type($method, $status_code, $request_url, $configuration, $response_data);
        }
      }
    }

    return $object;
  }

}