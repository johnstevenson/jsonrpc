<?php

class NewRequestTest extends RequestTests\Base
{

  public function testValidNewRequest()
  {
    $data = array('method' => 'foobar', 'params' => array(6), 'id' => 1);
    $expects = '{"jsonrpc": "2.0", "method": "foobar", "params": [6], "id": 1}';
    $json = $this->getRequestJson($data, $expects);
    $this->assertEquals($expects, $json);
  }

  public function testValidNewRequestIsNotNotification()
  {
    $data = array('method' => 'foobar', 'params' => array(6), 'id' => 1);
    $request = $this->getRequest($data);
    $this->assertFalse($request->notification);
  }

  public function testValidNewNotification()
  {
    $data = array('method' => 'foobar', 'params' => array(6));
    $expects = '{"jsonrpc": "2.0", "method": "foobar", "params": [6]}';
    $json = $this->getRequestJson($data, $expects);
    $this->assertEquals($expects, $json);
  }

  public function testValidNewNotificationIsNotification()
  {
    $data = array('method' => 'foobar', 'params' => array(6));
    $request = $this->getRequest($data);
    $this->assertTrue($request->notification);
  }

  public function testValidNewRequestNamedParams()
  {
    $data = array('method' => 'foobar', 'params' => (object) array('arg1' => 6), 'id' => 1);
    $expects = '{"jsonrpc": "2.0", "method": "foobar", "params": {"arg1": 6}, "id": 1}';
    $json = $this->getRequestJson($data, $expects);
    $this->assertEquals($expects, $json);
  }

  public function testValidNewRequestWithIgnoredInvalidSpec()
  {
    $data = array('jsonrpc' => '3.0', 'method' => 'foobar', 'params' => array(6), 'id' => 1);
    $expects = '{"jsonrpc": "2.0", "method": "foobar", "params": [6], "id": 1}';
    $json = $this->getRequestJson($data, $expects);
    $this->assertEquals($expects, $json);
  }

}
