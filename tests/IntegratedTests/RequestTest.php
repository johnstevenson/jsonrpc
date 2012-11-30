<?php

class RequestTest extends IntegratedTests\Base
{

  public function testDividePositional()
  {
    $method = 'divide';
    $params = array(42, 6);

    $expects = 7;
    $this->assertTrue($this->client->call($method, $params));
    $this->assertEquals($expects, $this->client->result);
    $this->assertNull($this->client->batch);
    $this->assertEquals('', $this->client->error);
  }

  public function testDivideNamed()
  {
    $method = 'divide';
    $params = new stdClass();
    $params->divisor = 6;
    $params->dividend = 42;

    $expects = 7;
    $this->client->call($method, $params);
    $this->assertEquals($expects, $this->client->result);
  }

  public function testDivideNamedArray()
  {
    $method = 'divide';
    $params = array(
      'divisor' => 12,
      'dividend' => 96,
    );

    $expects = 8;
    $this->client->call($method, $params);
    $this->assertEquals($expects, $this->client->result);
  }

  public function testDivideError()
  {
    $method = 'divide';
    $params = array(42, 0);

    $this->assertFalse($this->client->call($method, $params));

    $this->assertEquals(-32000, $this->client->errorCode);
    $error = 'Server error (-32000): ' . TEST_DIVIDE_ERROR;
    $this->assertEquals($error, $this->client->error);
  }

  public function testPingNamed()
  {
    $method = 'ping';

    $params = array(
      'user' => array(
        'id' => 257,
        'name' => 'Fred',
      ),
      'msg' => 'Hello',
    );

    $params = (object) $params;

    $this->client->call($method, $params);
    $result = $this->client->result;
    $this->assertEquals('Hello Fred (257)', $result->reply);
    $this->assertEquals(get_class($this->transport->serverMethods), $result->class);
    $this->assertEquals('object', $result->type);
  }


  public function testPingNamedArray()
  {
    $method = 'ping';

    $params = array(
      'user' => array(
        'id' => 257,
        'name' => 'Fred',
      ),
      'msg' => 'Hello',
    );

    $this->client->call($method, $params);
    $result = $this->client->result;
    $this->assertEquals('Hello Fred (257)', $result->reply);
    $this->assertEquals(get_class($this->transport->serverMethods), $result->class);
    $this->assertEquals('object', $result->type);
  }

  public function testNotify()
  {
    $method = 'divide';
    $params = array(42, 6);

    $expects = '';
    $this->assertTrue($this->client->notify($method, $params));
    $this->assertEquals($expects, $this->client->output);
    $this->assertNull($this->client->result);
    $this->assertNull($this->client->batch);
    $this->assertEquals('', $this->client->error);

  }

  public function testBatch()
  {
    $method = 'divide';

    $this->client->batchOpen();
    $this->client->call($method, array(42, 6));
    $this->client->notify($method, array(42));
    $this->client->call($method, array(42, 8, true));
    $this->assertTrue($this->client->batchSend());

    $json = '[
      {"jsonrpc": "2.0", "result": 7, "id": 1},
      {"jsonrpc": "2.0", "result": 5, "id": 2}
    ]';

    $expects = Helpers::fmt($json);
    $this->assertEquals($expects, Helpers::fmt($this->client->output));

    $this->assertEquals(7, $this->client->batch[0]->result);
    $this->assertEquals(5, $this->client->batch[1]->result);

    $this->assertNull($this->client->result);
    $this->assertEquals('', $this->client->error);
  }

  public function testBatchErrors()
  {
    $method = 'divide';
    $params = array(42, 0);

    $this->client->batchOpen();
    $this->client->call($method, $params);
    $this->client->notify($method, array(42));
    $this->client->call($method, $params);
    $this->assertTrue($this->client->batchSend());

    $json = '[
      {"jsonrpc":"2.0","error":{"code":-32000,"message":"Server error","data":"Cannot divide by zero"},"id":1},
      {"jsonrpc":"2.0","error":{"code":-32000,"message":"Server error","data":"Cannot divide by zero"},"id":2}
    ]';

    $expects = Helpers::fmt($json);
    $this->assertEquals($expects, Helpers::fmt($this->client->output));

    $this->assertNull($this->client->result);
    $this->assertEquals('', $this->client->error);
  }


  public function testBatchNotify()
  {
    $method = 'divide';
    $params = array(42, 0);

    $this->client->batchOpen();
    $this->client->notify($method, $params);
    $this->client->notify($method, array(42));
    $this->assertTrue($this->client->batchSend());
    $this->assertEquals('', $this->client->output);
    $this->assertNull($this->client->batch);
    $this->assertNull($this->client->result);
    $this->assertEquals('', $this->client->error);
  }

}

