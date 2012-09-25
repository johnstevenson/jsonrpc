<?php

use \JsonRpc\Base\Rpc;

class ResponseGeneralTest extends ResponseTests\Base
{

  public function testValidResponseResult()
  {
    $data = '{"jsonrpc": "2.0", "result": 6, "id": 1}';
    $expects = '';
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testValidResponseError()
  {
    $data = '{"jsonrpc": "2.0", "error": {"code": -32601, "message": "Method not found"}, "id": 1}';
    $expects = '';
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testValidResponseErrorWithIdNull()
  {
    $data = '{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}';
    $expects = '';
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testValidResponseIdWithNumberZero()
  {
    $data = '{"jsonrpc": "2.0", "result": 6, "id": 0}';
    $expects = '';
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testValidResponseIdWithString()
  {
    $data = '{"jsonrpc": "2.0", "result": 6, "id": "req1"}';
    $expects = '';
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseErrorWithIdNull()
  {
    $data = '{"jsonrpc": "2.0", "error": {"code": -32601, "message": "Method not found"}, "id": null}';
    $expects = Rpc::getErrorMsg('id');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseResultAndError()
  {
    $data = '{"jsonrpc": "2.0", "result": 6, "error": {"code": -32601, "message": "Method not found"}, "id": null}';
    $expects = Rpc::getErrorMsg('');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseResultAndErrorMissing()
  {
    $data = '{"jsonrpc": "2.0", "id": 1}';
    $expects = Rpc::getErrorMsg('');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseResultWithIdNull()
  {
    $data = '{"jsonrpc": "2.0", "result": 6, "id": null}';
    $expects = Rpc::getErrorMsg('id');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseResultWithIdStringEmpty()
  {
    $data = '{"jsonrpc": "2.0", "result": 6, "id": ""}';
    $expects = Rpc::getErrorMsg('id');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseResultWithIdFractional()
  {
    $data = '{"jsonrpc": "2.0", "result": 6, "id": 1.24}';
    $expects = Rpc::getErrorMsg('id');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseResultWithIdTrue()
  {
    $data = '{"jsonrpc": "2.0", "result": 6, "id": true}';
    $expects = Rpc::getErrorMsg('id');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseResultWithIdFalse()
  {
    $data = '{"jsonrpc": "2.0", "result": 6, "id": false}';
    $expects = Rpc::getErrorMsg('id');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }


  public function testInvalidResponseResultWithIdArray()
  {
    $data = '{"jsonrpc": "2.0", "result": 6, "id": [1]}';
    $expects = Rpc::getErrorMsg('id');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }

  public function testInvalidResponseResultWithIdObject()
  {
    $data = '{"jsonrpc": "2.0", "result": 6, "id": {"value": 1}}';
    $expects = Rpc::getErrorMsg('id');
    $fault = $this->getResponseFault($data);
    $this->assertEquals($expects, $fault);
  }


}
