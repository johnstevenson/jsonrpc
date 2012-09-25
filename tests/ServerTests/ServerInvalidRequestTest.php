<?php

use \JsonRpc\Base\Rpc;

class ServerInvalidRequestTest extends ServerTests\Base
{

  public function testInvalidRequestSpec()
  {
    $data = '{"jsonrpc": "1.0", "method": "foobar", "params": [6], "id": 1}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": 1}';
    $errorData = Rpc::getErrorMsg('jsonrpc');
    $json = $this->getResponseJsonError($data, $expects, $errorData);
    $this->assertEquals($expects, $json);
  }

  public function testInvalidRequestSpecMissing()
  {
    $data = '{"method": "foobar", "params": [6], "id": 1}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": 1}';
    $errorData = Rpc::getErrorMsg('jsonrpc', false);
    $json = $this->getResponseJsonError($data, $expects, $errorData);
    $this->assertEquals($expects, $json);
  }

  public function testInvalidRequestMethod()
  {
    $data = '{"jsonrpc": "2.0", "method": "", "params": [6], "id": 1}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": 1}';
    $errorData = Rpc::getErrorMsg('method');
    $json = $this->getResponseJsonError($data, $expects, $errorData);
    $this->assertEquals($expects, $json);
  }

  public function testInvalidRequestMethodMissing()
  {
    $data = '{"jsonrpc": "2.0", "params": [6], "id": 1}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": 1}';
    $errorData = Rpc::getErrorMsg('method', false);
    $json = $this->getResponseJsonError($data, $expects, $errorData);
    $this->assertEquals($expects, $json);
  }

  public function testInvalidRequestParam()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": "bar", "id": 1}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": 1}';
    $errorData = Rpc::getErrorMsg('params');
    $json = $this->getResponseJsonError($data, $expects, $errorData);
    $this->assertEquals($expects, $json);
  }

  public function testInvalidRequestId()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6], "id": [1]}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}';
    $errorData = Rpc::getErrorMsg('id');
    $json = $this->getResponseJsonError($data, $expects, $errorData);
    $this->assertEquals($expects, $json);
  }

  public function testInvalidRequestIdWithNull()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6], "id": null}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}';
    $errorData = Rpc::getErrorMsg('id');
    $json = $this->getResponseJsonError($data, $expects, $errorData);
    $this->assertEquals($expects, $json);
  }

  public function testInvalidNotification()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": "bar"}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}';
    $errorData = Rpc::getErrorMsg('params');
    $json = $this->getResponseJsonError($data, $expects, $errorData);
    $this->assertEquals($expects, $json);
  }

  public function testInvalidBatchEmpty()
  {
    $data = '[]';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }

  public function testInvalidBatchOne()
  {
    $data = '[3]';
    $expects = '[{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}]';
    $errorData = Rpc::getErrorMsg('');
    $json = $this->getResponseJsonError($data, $expects, $errorData);
    $this->assertEquals($expects, $json);
  }

  public function testInvalidBatchMany()
  {
    $data = '[1, 2]';

    $expects = '[
      {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null},
      {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}
    ]';

    $error = Rpc::getErrorMsg('');
    $errorData = array($error, $error);
    $json = $this->getResponseJsonError($data, $expects, $errorData);
    $this->assertEquals($expects, $json);
  }

}

