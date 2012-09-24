<?php
namespace Response;

use \JsonRpc\Base\Rpc;
use \JsonRpc\Base\Response;
use \Helpers;


class Base extends \PHPUnit_Framework_TestCase
{

  protected function getResponseJson($data, &$expects)
  {
    $expects = Helpers::fmt($expects);
    $response = new Response();
    $response->create($data);
    return Helpers::fmt($response->toJson());
  }

  protected function getResponseFault($data)
  {
    $struct = json_decode($data);
    $response = new Response($struct);
    $response->create($struct);
    return $response->fault;
  }

  protected function getResponse($struct)
  {
    return new Response($struct);
  }

  protected function getResponseJsonError($data, &$expects, $errorData)
  {
    $expects = Helpers::addErrors($expects, $errorData);
    return $this->getResponseJson($data, $expects);
  }

}

