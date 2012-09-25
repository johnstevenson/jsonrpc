<?php
namespace ClientTests;

class Transport
{

  public $output = '';
  public $error = '';

  public function send($method, $url, $json, $headers = array())
  {
    # we set output before calling send
    return !empty($this->output);
  }


}
