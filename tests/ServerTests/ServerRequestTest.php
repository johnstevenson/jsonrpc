<?php

class ServerRequestTest extends ServerTests\Base
{

  public function setUp()
  {
    $this->methods = $this->methods ?: new MethodsClass();
  }


  /**
  * Simple test calling divide
  *
  */
  public function testClassDivide()
  {
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "divide", "params": [42, 6], "id": 1}';
    $expects = '{"jsonrpc": "2.0", "result": 7, "id": 1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }


  /**
  * Calls divide with named params.
  *
  */
  public function testClassDivideNamed()
  {
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "divide", "params": {"dividend": 20, "divisor": 4}, "id": 1}';
    $expects = '{"jsonrpc": "2.0", "result": 5, "id": 1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }


  /**
  * Calls divide with named params. Checks that the out of order
  * params are given to the method in the correct order
  *
  */
  public function testClassDivideNamedWrongOrder()
  {
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "divide", "params": {"divisor": 8, "int": true, "dividend": 20}, "id": 1}';
    $expects = '{"jsonrpc": "2.0", "result": 2, "id": 1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }



  /**
  * Checks that an error from the class is read and returned
  *
  */
  public function testClassDivideError()
  {
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "divide", "params": [42, 0], "id": 1}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32000, "message": "Server error"}, "id": 1}';
    $errorData = TEST_DIVIDE_ERROR;
    $json = $this->getResponseJsonError($data, $expects, $errorData);
    $this->assertEquals($expects, $json);
  }


  /**
  * Checks that an error from the static class is read and returned
  *
  */
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


  /**
  * Checks that using __call does not impact positional param checks
  *
  */
  public function testClassCallPositional()
  {
    $this->methods = new MethodsClassCall();
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "divide", "params": [42, 6], "id": 1}';
    $expects = '{"jsonrpc": "2.0", "result": 7, "id": 1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }


  /**
  * Checks that using __call does not impact named param checks
  *
  */
  public function testClassCallNamed()
  {
    $this->methods = new MethodsClassCall();
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "ping",
      "params": {"msg": "Hello", "user": {"name": "Fred", "id": 257}},
      "id": 1}';
    $expects = '{"jsonrpc": "2.0",
      "result": {"reply": "Hello Fred (257)", "class": "MethodsStatic", "type": "object"},
      "id": 1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }


  /**
  * Checks that using __call does not impact named param checks
  * calling setObjectsAsArrays()
  *
  */
  public function testClassCallNamedAssoc()
  {
    $this->methods = new MethodsClassCall();
    parent::setUp();
    $this->server->setObjectsAsArrays();

    $data = '{"jsonrpc": "2.0", "method": "ping",
      "params": {"msg": "Hello", "user": {"name": "Fred", "id": 257}},
      "id": 1}';
    $expects = '{"jsonrpc": "2.0",
      "result": {"reply": "Hello Fred (257)", "class": "MethodsStatic", "type": "array"},
      "id": 1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }


  /**
  * Checks that missing positional params are reported as an error
  *
  */
  public function testClassMissingParamsPositional()
  {
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "divide", "params": [42], "id": 1}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32602, "message": "Invalid params"}, "id" :1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }


  /**
  * Checks that missing named params are are reported as an error
  *
  */
  public function testClassMissingParamsNamed()
  {
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "ping", "params": {"msg": "Hello"}, "id": 1}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32602, "message": "Invalid params"}, "id" :1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }


  /**
  * Checks that missing params to __call are are reported as an internal error
  * Note this is actually set in the __call method to avoid PHPUnit exceptions being reported
  *
  */
  public function testClassMissingParamsCall()
  {
    $this->methods = new MethodsClassCall();
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "divide", "params": [42], "id": 1}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32603, "message": "Internal error"}, "id" :1}';
    $json = $this->getResponseJson($data, $expects);

    $this->assertEquals($expects, $json);
  }


  /**
  * Checks a notify with missing params returns nothing
  *
  */
  public function testNotifyMissingParams()
  {
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "divide", "params": [42]}';
    $expects = '';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }


  /**
  * Checks that a notify with missing params to __call, which will invoke a caugth
  * exception, returns nothing.
  *
  */
  public function testNotifyMissingParamsCall()
  {
    $this->methods = new MethodsClassCall();
    parent::setUp();
    $data = '{"jsonrpc": "2.0", "method": "divide", "params": [42]}';
    $expects = '';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);

  }


  /**
  * Checks that a raw function cannot be passed as a method hander
  *
  */
  public function testInvalidRawFunction()
  {
    $this->methods = 'divide';
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "divide", "params": [42, 6], "id": 1}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32601, "message": "Method not found"}, "id": 1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }


  /**
  * Checks that a closure cannot be passed as a method hander
  *
  */
  public function testInvalidClosure()
  {
    $this->methods = methodsJsonRpcClosure();
    parent::setUp();

    $data = '{"jsonrpc": "2.0", "method": "divide", "params": [42, 6], "id": 1}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32601, "message": "Method not found"}, "id": 1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }

}
