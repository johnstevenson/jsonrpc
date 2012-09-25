<?php

use \JsonRpc\Base\Rpc;

class RequestParamTest extends RequestTests\Base
{

  public function testValidRequestParamMissing()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "id": 1}';
    $expects = '';
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testValidRequestParamWithArray()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6, "param"], "id": 1}';
    $expects = '';
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testValidRequestParamWithArrayEmpty()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [], "id": 1}';
    $expects = '';
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testValidRequestParamWithObject()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": {"arg1": 3, "arg2": true}, "id": 1}';
    $expects = '';
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testValidRequestParamWithObjectEmpty()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": {}, "id": 1}';
    $expects = '';
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestParamWithString()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": "bar", "id": 1}';
    $expects = Rpc::getErrorMsg('params');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestParamWithInteger()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": 1, "id": 1}';
    $expects = Rpc::getErrorMsg('params');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestParamWithFloat()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": 2.64, "id": 1}';
    $expects = Rpc::getErrorMsg('params');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestParamWithTrue()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": true, "id": 1}';
    $expects = Rpc::getErrorMsg('params');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestParamWithFalse()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": false, "id": 1}';
    $expects = Rpc::getErrorMsg('params');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidRequestParamWithNull()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": null, "id": 1}';
    $expects = Rpc::getErrorMsg('params');
    $fault = $this->getRequestFault($data);
    $this->assertEquals($expects, $fault);
  }

}
