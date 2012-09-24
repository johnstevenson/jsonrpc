<?php

use \JsonRpc\Base\Rpc;

class NewResponseTest extends Response\Base
{

  public function testValidNewResponse()
  {
    $data = array('result' => 6, 'id' => 1);
    $expects = '{"jsonrpc": "2.0", "result": 6, "id": 1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }

  public function testValidNewResponseWithIgnoredInvalidSpec()
  {
    $data = array('jsonrpc' => '3.0', 'result' => 6, 'id' => 1);
    $expects = '{"jsonrpc": "2.0", "result": 6, "id": 1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }

  public function testValidNewResponseErrorCode()
  {
    $data = array('error' => -32601, 'id' => 1);
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32601, "message": "Method not found"}, "id": 1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }

  public function testValidNewResponseErrorData()
  {
    $errorData = Rpc::getErrorMsg('');
    $error = array('code' => Rpc::ERR_REQUEST, 'data' => $errorData);
    $data = array('error' => $error, 'id' => 1);

    $expects = '{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": 1}';
    $json = $this->getResponseJsonError($data, $expects, $errorData);

    $this->assertEquals($expects, $json);
  }

  public function testValidNewResponseErrorServer()
  {
    $errorData = 'Please try later';
    $error = array('code' => -32000, 'message' => 'Server fault', 'data' => $errorData);
    $data = array('error' => $error, 'id' => 1);

    $expects = '{"jsonrpc": "2.0", "error": {"code": -32000, "message": "Server fault"}, "id": 1}';
    $json = $this->getResponseJsonError($data, $expects, $errorData);

    $this->assertEquals($expects, $json);
  }
  /*
  public function testInvalidNewResponseErrorServer()
  {
    $error = array('code' => -31999, 'message' => 'Server fault', 'data' => 'Please try later');
    $data = array('error' => $error, 'id' => 1);

    $expects = '{"jsonrpc": "2.0", "error": {"code": -32603, "message": "Internal error"}, "id": 1}';
    $json = $this->getResponseJson($data, $expects);

    $this->assertEquals($expects, $json);
  }
  */


}
