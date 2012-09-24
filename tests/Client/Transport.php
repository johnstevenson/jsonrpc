<?php
namespace Client;

class Transport
{

  public $output = '';
  public $error = '';
  public $input = '';
  public $method = '';


  public function send($method, $uri, $json, $headers = array())
  {

    $this->method = $method;
    $this->input = $json;

    return true;

  }


}
