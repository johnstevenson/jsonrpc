<?php
namespace RequestTests;

use \JsonRpc\Base\Rpc;
use \JsonRpc\Base\Request;
use \Helpers;


class Base extends \PHPUnit_Framework_TestCase
{

  protected function getRequestJson($data, &$expects)
  {
    $expects = Helpers::fmt($expects);
    $request = new Request($data);
    return Helpers::fmt($request->toJson());
  }

  protected function getRequestFault($data)
  {
    $struct = json_decode($data);
    $request = new Request($struct);
    return $request->fault;
  }

  protected function getRequest($struct)
  {
    return new Request($struct);
  }

}

