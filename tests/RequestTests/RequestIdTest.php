<?php

use \JsonRpc\Base\Rpc;

class RequestIdTest extends RequestTests\Base
{

  public function testValidRequestId()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6], "id": 1}';
    $expects = '';
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testValidRequestNotification()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6]}';
    $expects = '';
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testValidRequestIdWithNumberZero()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6], "id": 0}';
    $expects = '';
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testValidRequestIdWithString()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6], "id": "req1"}';
    $expects = '';
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestIdWithStringEmpty()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6], "id": ""}';
    $expects = Rpc::getErrorMsg('id');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestIdWithFractional()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6], "id": 1.24}';
    $expects = Rpc::getErrorMsg('id');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestIdWithTrue()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6], "id": true}';
    $expects = Rpc::getErrorMsg('id');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestIdWithFalse()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6], "id": false}';
    $expects = Rpc::getErrorMsg('id');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestIdWithNull()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6], "id": null}';
    $expects = Rpc::getErrorMsg('id');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestIdWithArray()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6], "id": [1]}';
    $expects = Rpc::getErrorMsg('id');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestIdWithObject()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6], "id": {"value": 1}}';
    $expects = Rpc::getErrorMsg('id');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

}
