<?php
namespace ServerTests;

use \JsonRpc\Server;
use \JsonRpc\Base\Rpc;
use \Helpers;


class Base extends \PHPUnit_Framework_TestCase
{

  public $server = null;
  public $methods = null;
  public $transport = null;


  public function setUp()
  {
    $this->server = new Server($this->methods);
    $this->transport = $this->transport ?: new Transport();
    $this->server->setTransport($this->transport);
  }

  protected function getResponseJson($data, &$expects)
  {
    $expects = Helpers::fmt($expects);
    $this->transport->input = $data;
    $this->server->receive();
    return Helpers::fmt($this->transport->output);
  }

  protected function getResponseJsonError($data, &$expects, $errorData)
  {
    $expects = Helpers::addErrors($expects, $errorData);
    return $this->getResponseJson($data, $expects);
  }

}

