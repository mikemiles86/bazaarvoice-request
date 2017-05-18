<?php

namespace BazaarvoiceRequest\Response;

abstract class BazaarvoiceResponseBase implements BazaarvoiceResponseInterface {

  /**
   * HTTP Method
   *
   * @var string
   */
  protected $method;

  /**
   * HTTP status code returned.
   *
   * @var string
   */
  protected $status_code;

  /**
   * URL Requested
   *
   * @var string
   */
  protected $request_url;

  /**
   * Additional request configurations passed.
   *
   * @var array
   */
  protected $request_configuration;

  /**
   * Errors returned.
   *
   * @var array
   */
  protected $errors;

  /**
   * RAW response data.
   *
   * @var array
   */
  protected $response;


  public function __construct($method, $status_code, $request_url, array $request_configuration = [], array $response = []) {

    $this->method = $method;
    $this->status_code = $status_code;
    $this->request_url = $request_url;
    $this->request_configuration = $request_configuration;

    $errors = [];

    if (isset($response['Errors'])) {
      $errors = array_merge($errors, $response['Errors']);
    }

    if (isset($response['FormErrors'])) {
      $errors = array_merge($errors, $response['FormErrors']);
    }

    $this->errors = $errors;
    $this->response = $response;
  }

  public function getMethod() {
    return $this->method;
  }

  public function getStatusCode() {
    return $this->status_code;
  }

  public function getRequestUrl() {
    return $this->request_url;
  }

  public function getConfiguration($element_name = null) {

    $configuration = null;
    if ($element_name && isset($this->request_configuration[$element_name])) {
      $configuration = $this->request_configuration[$element_name];
    }
    elseif (!$element_name) {
      $configuration = $this->request_configuration;
    }

    return $configuration;
  }

  public function getErrors() {
    return $this->errors;
  }

  public function hasErrors() {
    return (count($this->errors) > 0);
  }

  public function getResponse($element_name = null) {

    $response = null;
    if ($element_name && isset($this->response[$element_name])) {
      $response = $this->response[$element_name];
    }
    elseif (!$element_name) {
      $response = $this->response;
    }
    return $response;
  }
}