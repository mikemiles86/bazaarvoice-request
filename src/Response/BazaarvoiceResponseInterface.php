<?php

namespace BazaarvoiceRequest\Response;

/**
 * Interface BazaarvoiceResponseInterface
 * @package BazaarvoiceRequest\Response
 */
interface BazaarvoiceResponseInterface {

  /**
   * Return HTTP Method used in request.
   *
   * @return string
   */
  public function getMethod();

  /**
   * Return HTTP Status code from request.
   *
   * @return string
   */
  public function getStatusCode();

  /**
   * Get URL that request was made to.
   *
   * @return string
   */
  public function getRequestUrl();

  /**
   * Get configuration of array or specific element from the array.
   *
   * @param string|null $element_name
   *   (optional) name of configuration element to retrieve.
   *
   * @return array|mixed|null
   */
  public function getConfiguration($element_name = null);

  /**
   * Get raw response data, or sub element from raw response.
   *
   * @param string|null $element_name
   *   (optional) name of response element to return.
   *
   * @return array|mixed|null
   */
  public function getResponse($element_name = null);

  /**
   * Return errors returned by request.
   *
   * @return array
   */
  public function getErrors();

  /**
   * Boolean check if there were errors in this request.
   *
   * @return bool
   */
  public function hasErrors();

}