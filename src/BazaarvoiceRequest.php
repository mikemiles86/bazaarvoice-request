<?php

namespace BazaarvoiceRequest;

class BazaarvoiceRequest implements BazaarvoiceRequestInterface {

  private static $api_version = '5.4';
  protected $domain = 'api.bazaarvoice.com';
  protected $apiKey;
  protected $use_stage = FALSE;
  protected $errors;


  public function __construct($apiKey, $use_stage = FALSE) {
    $this->apiKey = $apiKey;
    $this->use_stage = ($use_stage === TRUE);
    $this->errors = [];
  }

  public function apiRequest($endpoint, array $parameters = [], $method = 'GET', array $options = []) {
    // Get the URL for the request.
    $request_url = $this->buildUrl($endpoint, $parameters);
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

    // Create a new Guzzle client object
    $client = new GuzzleHttp\Client($request_options);
    $response_data = NULL;
    // Attempt to get a response.
    try {
      $response = $client->request($method, $request_url);
      $status_code = $response->getStatusCode();
      $stream_size = $response->getBody()->getSize();
      $response_data = Json::decode($response->getBody()->read($stream_size));
      if ($status_code < 200 || $status_code > 299) {
        throw new ConnectorException($data['message'], $data['code'], $data);
      }
    }
    catch (RequestException $e) {
      //@TODO: what to do with this expection?
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

}