<?php

use \JsonRpc\Base\Rpc;

class ResponseSpecTest extends ResponseTests\Base
{

  public function testValidResponseSpec()
  {
    $data = '{"jsonrpc": "2.0", "result": 6, "id": 1}';
    $expects = '';
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseSpecMissing()
  {
    $data = '{"result": 6, "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc', false);
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseSpecWithStringLower()
  {
    $data = '{"jsonrpc": "1.0", "result": 6, "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseSpecWithStringHigher()
  {
    $data = '{"jsonrpc": "2.8", "result": 6, "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseSpecWithStringEmpty()
  {
    $data = '{"jsonrpc": "", "result": 6, "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseSpecWithNumber()
  {
    $data = '{"jsonrpc": 2, "result": 6, "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseSpecWithTrue()
  {
    $data = '{"jsonrpc": true, "result": 6, "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseSpecWithFalse()
  {
    $data = '{"jsonrpc": false, "result": 6, "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseSpecWithNull()
  {
    $data = '{"jsonrpc": null, "result": 6, "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseSpecWithArray()
  {
    $data = '{"jsonrpc": [2], "result": 6, "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseSpecWithObject()
  {
    $data = '{"jsonrpc": {"version": "2.0"}, "result": 6, "id": 1}';
    $expects = Rpc::getErrorMsg('jsonrpc');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

}
