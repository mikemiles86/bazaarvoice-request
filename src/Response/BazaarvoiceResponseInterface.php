<?php

namespace BazaarvoiceRequest\Response;

interface BazaarvoiceResponseInterface {

  public function getMethod();

  public function getStatusCode();

  public function getRequestUrl();

  public function getConfiguration($element_name = null);

  public function getResponse($element_name = null);

  public function getErrors();

  public function hasErrors();


}