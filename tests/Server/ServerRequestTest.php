<?php

require('Methods.php');

class ServerRequestTest extends Server\Base
{

  public function setUp()
  {
    $this->methods = $this->methods ?: new MethodsClass();
  }


  public function testClassDivide()
  {
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "divide", "params": [42, 6], "id": 1}';
    $expects = '{"jsonrpc": "2.0", "result": 7, "id": 1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }

  public function testClassDivideNamed()
  {
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "divide", "params": {"divisor": 8, "int": true, "dividend": 20}, "id": 1}';
    $expects = '{"jsonrpc": "2.0", "result": 2, "id": 1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }


  public function testClassDivideError()
  {
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "divide", "params": [42, 0], "id": 1}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32000, "message": "Server error"}, "id": 1}';
    $errorData = TEST_DIVIDE_ERROR;
    $json = $this->getResponseJsonError($data, $expects, $errorData);
    $this->assertEquals($expects, $json);
  }

  public function testClassStaticDivideError()
  {
    $this->methods = 'MethodsStatic';
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "divide", "params": [42, 0], "id": 1}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32000, "message": "Server error"}, "id": 1}';
    $errorData = TEST_DIVIDE_ERROR;
    $json = $this->getResponseJsonError($data, $expects, $errorData);
    $this->assertEquals($expects, $json);
  }

  public function testClassCall()
  {
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "ping",
      "params": {"msg": "Hello", "user": {"name": "Fred", "id": 257}},
      "id": 1}';
    $expects = '{"jsonrpc": "2.0",
      "result": {"reply": "Hello Fred (257)", "class": "MethodsClass"},
      "id": 1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }

  public function testClassCallStatic()
  {
    $this->methods = 'MethodsStatic';
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "ping",
      "params": ["Hello", {"name": "Fred", "id": 257}],
      "id": 1}';
    $expects = '{"jsonrpc": "2.0",
      "result": {"reply": "Hello Fred (257)", "class": "MethodsStatic"},
      "id": 1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }

}
