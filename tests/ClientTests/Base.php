<?php
namespace ClientTests;

use \JsonRpc\Client;
use \JsonRpc\Base\Rpc;
use \Helpers;


class Base extends \PHPUnit_Framework_TestCase
{

  public $client = null;
  public $transport = null;


  public function setUp()
  {
    $this->transport = $this->transport ?: new Transport();
    $this->client = new Client('dummy', $this->transport);
  }

  protected function getResponseJson($data, &$expects)
  {
    $expects = Helpers::fmt($expects);
    $this->transport->input = $data;
    return Helpers::fmt($this->transport->output);
  }

  protected function getResponseJsonError($data, &$expects, $errorData)
  {
    $expects = Helpers::addErrors($expects, $errorData);
    return $this->getResponseJson($data, $expects);
  }

}

