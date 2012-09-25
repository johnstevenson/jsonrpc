<?php

use \JsonRpc\Base\Rpc;

class RequestSpecTest extends RequestTests\Base
{

  public function testValidRequestSpec()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6], "id": 1}';
    $expects = '';
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestSpecMissing()
  {
    $data = '{"method": "foobar", "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc', false);
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestSpecWithStringLower()
  {
    $data = '{"jsonrpc": "1.0", "method": "foobar", "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestSpecWithStringHigher()
  {
    $data = '{"jsonrpc": "2.8", "method": "foobar", "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestSpecWithStringEmpty()
  {
    $data = '{"jsonrpc": "", "method": "foobar", "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestSpecWithNumber()
  {
    $data = '{"jsonrpc": 2, "method": "foobar", "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestSpecWithTrue()
  {
    $data = '{"jsonrpc": true, "method": "foobar", "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestSpecWithFalse()
  {
    $data = '{"jsonrpc": false, "method": "foobar", "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestSpecWithNull()
  {
    $data = '{"jsonrpc": null, "method": "foobar", "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestSpecWithArray()
  {
    $data = '{"jsonrpc": [2], "method": "foobar", "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestSpecWithObject()
  {
    $data = '{"jsonrpc": {"version": "2.0"}, "method": "foobar", "params": [6], "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

}
