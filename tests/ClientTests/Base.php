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

}

