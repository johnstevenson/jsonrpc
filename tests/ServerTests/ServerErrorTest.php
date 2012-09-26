<?php

class ServerErrorTest extends ServerTests\Base
{

  public function testParseErrorRequest()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar, "params": "bar", "baz]';
    $expects = '{"jsonrpc": "2.0","error": {"code": -32700, "message":"Parse error"}, "id": null}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }

  public function testParseErrorBatch()
  {
    $data = '[{"jsonrpc": "2.0", "method": "sum", "params": [1,2,4], "id": "1"},{"jsonrpc": "2.0", "method"]';
    $expects = '{"jsonrpc": "2.0","error": {"code": -32700, "message":"Parse error"}, "id": null}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }

  public function testRequestWrongMethod()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6], "id": 1}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32601, "message": "Method not found"}, "id": 1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }

  public function testNotifyWrongMethod()
  {
    $data = '{"jsonrpc": "2.0", "method": "foobar", "params": [6]}';
    $expects = '';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);
  }

  public function testExceptionWithLogger()
  {

    $this->methods = 'MethodsException';
    parent::setUp();

    $logger = new ServerLogger();
    $this->server->setLogger($logger);

    $data = '{"jsonrpc": "2.0", "method": "divide", "params": [42, 0], "id": 1}';
    $expects = '{"jsonrpc": "2.0", "error": {"code": -32603, "message": "Internal error"}, "id": 1}';
    $json = $this->getResponseJson($data, $expects);
    $this->assertEquals($expects, $json);

    $expects = 500;
    $this->assertEquals($expects, $logger->level);

    $expects = 'divide';
    $this->assertContains($expects, $logger->message);

  }

}
