<?php

class ServerErrorTest extends Server\Base
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


}
