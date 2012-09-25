<?php

use \JsonRpc\Base\Rpc;

class RequestMethodTest extends RequestTests\Base
{

  public function testValidRequestMethod()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6], "id": 1}';
    $expects = '';
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestMethodMissing()
  {
    $data = '{"jsonrpc": "2.0", "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('method', false);
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestMethodWithStringEmpty()
  {
    $data = '{"jsonrpc": "2.0", "method": "", "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('method');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestMethodWithInteger()
  {
    $data = '{"jsonrpc": "2.0", "method": 1, "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('method');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestMethodWithFloat()
  {
    $data = '{"jsonrpc": "2.0", "method": 2.46, "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('method');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestMethodWithTrue()
  {
    $data = '{"jsonrpc": "2.0", "method": true, "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('method');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestMethodWithFalse()
  {
    $data = '{"jsonrpc": "2.0", "method": false, "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('method');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestMethodWithNull()
  {
    $data = '{"jsonrpc": "2.0", "method": null, "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('method');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestMethodWithArray()
  {
    $data = '{"jsonrpc": "2.0", "method": [1,2,3], "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('method');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestMethodWithObject()
  {
    $data = '{"jsonrpc": "2.0", "method": {"name": 2}, "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('method');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

}
