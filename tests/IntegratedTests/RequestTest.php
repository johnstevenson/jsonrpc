<?php

class RequestTest extends IntegratedTests\Base
{

  public function testDividePositional()
  {
    $method = 'divide';
    $params = array(42, 6);

    $expects = 7;
    $this->client->call($method, $params);
    $this->assertEquals($expects, $this->client->result);
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

  public function testDivideError()
  {
    $method = 'divide';
    $params = array(42, 0);

    $this->client->call($method, $params);
    $error = $this->client->error;
    $this->assertEquals(-32000, $error->code);
    $this->assertEquals('Server error', $error->message);
    $this->assertEquals(TEST_DIVIDE_ERROR, $error->data);
  }

  public function testPing()
  {
    $method = 'ping';

    $params = array(
      'user' => array(
        'id' => 257,
        'name' => 'Fred',
      ),
      'msg' => 'Hello',
    );

    $this->client->call($method, (object) $params);
    $result = $this->client->result;
    $this->assertEquals('Hello Fred (257)', $result->reply);
    $this->assertEquals(get_class($this->transport->serverMethods), $result->class);
  }

  public function testNotify()
  {
    $method = 'divide';
    $params = array(42, 6);

    $expects = '';
    $this->client->notify($method, $params);
    $this->assertEquals($expects, $this->client->output);

  }

  public function testBatch()
  {
    $method = 'divide';
    $params = array(42, 6);

    $this->client->batchOpen();
    $this->client->call($method, $params);
    $this->client->notify($method, $params);
    $this->client->call($method, $params);
    $this->client->batchSend();




  }




}

