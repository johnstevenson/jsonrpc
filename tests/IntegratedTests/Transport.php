<?php
namespace IntegratedTests;

class Transport
{

  public $output = '';
  public $error = '';

  public $serverTransport = null;
  public $serverMethods = null;


  public function __construct()
  {
    $this->serverTransport = new \ServerTests\Transport;
    $this->serverMethods = new \MethodsClass();
  }


  public function send($method, $url, $json, $headers = array())
  {

    $this->serverTransport->input = $json;
    $server = new \JsonRpc\Server($this->serverMethods);
    $server->setTransport($this->serverTransport);
    $server->receive();
    $this->output = $this->serverTransport->output;
    return true;

  }


}
