<?php

class ClientRequestTest extends ClientTests\Base
{

  /**
  * Checks that an unordered batch is returned ordered in client->batch
  *
  */
  public function testValidBatchUnordered()
  {

    $method = 'divide';
    $params1 = array(42, 6);
    $params2 = array(42, 7);

    $this->client->batchOpen();
    $this->client->call($method, $params1);
    $this->client->call($method, $params2);
    $this->client->call($method, $params1);
    $this->client->call($method, $params2);

    $json = '[
      {"jsonrpc": "2.0", "result": 6, "id": 2},
      {"jsonrpc": "2.0", "result": 6, "id": 4},
      {"jsonrpc": "2.0", "result": 7, "id": 1},
      {"jsonrpc": "2.0", "result": 7, "id": 3}
    ]';

    $this->transport->output = $json;

    $this->assertTrue($this->client->batchSend());

    $expects = '[
      {"jsonrpc": "2.0", "result": 7, "id": 1},
      {"jsonrpc": "2.0", "result": 6, "id": 2},
      {"jsonrpc": "2.0", "result": 7, "id": 3},
      {"jsonrpc": "2.0", "result": 6, "id": 4}
    ]';

    $batch = json_encode($this->client->batch);

    $this->assertEquals(Helpers::fmt($expects), Helpers::fmt($batch));
    $this->assertNull($this->client->result);

  }


  /**
  * Checks that an invalid response is reported as an error
  *
  */
  public function testInvalidResponse()
  {

    $method = 'divide';
    $params = array(42, 6);

    $json = '{"jsonrpc": "2.0", "result": 6, "error": {"code": -32601, "message": "Method not found"}, "id": null}';
    $this->transport->output = $json;

    $this->assertFalse($this->client->call($method, $params));
    $expects = \JsonRpc\Client::ERR_RPC_RESPONSE;
    $this->assertStringStartsWith($expects, $this->client->error);
    $this->assertNull($this->client->result);
    $this->assertNull($this->client->batch);

  }

  /**
  * Checks that an invalid response is reported as an error
  *
  */
  public function testInvalidNotifyResponse()
  {

    $method = 'divide';
    $params = array(42, 6);

    $json = '{"jsonrpc": "2.0", "result": 7, "id": 1}';
    $this->transport->output = $json;

    $this->assertFalse($this->client->notify($method, $params));
    $expects = \JsonRpc\Client::ERR_RPC_RESPONSE;
    $this->assertStringStartsWith($expects, $this->client->error);
    $this->assertNull($this->client->result);
    $this->assertNull($this->client->batch);

  }


  /**
  * Checks that an invalid batch response is reported as an error
  *
  */
  public function testInvalidBatchResponse()
  {

    $method = 'divide';
    $params = array(42, 6);

    $this->client->batchOpen();
    $this->client->call($method, $params);
    $this->client->call($method, $params);
    $this->client->call($method, $params);
    $this->client->call($method, $params);

    $json = '[
      {"jsonrpc": "2.0", "result": 7, "id": 1},
      {"jsonrpc": "2.0", "result": 7, "id": 2},
      {"jsonrpc": "2.0", "result": 7, "id": null},
      {"jsonrpc": "2.0", "result": 7, "id": 4}
    ]';

    $this->transport->output = $json;

    $this->assertFalse($this->client->batchSend());
    $expects = \JsonRpc\Client::ERR_RPC_RESPONSE;
    $this->assertStringStartsWith($expects, $this->client->error);
    $this->assertNull($this->client->result);
    $this->assertNull($this->client->batch);

  }


  /**
  * Checks that a parse error response from a batch is handled correctly
  *
  */
  public function testInvalidBatchParseError()
  {

    $method = 'divide';
    $params = array(42, 6);

    $this->client->batchOpen();
    $this->client->call($method, $params);
    $this->client->call($method, $params);
    $this->client->call($method, $params);
    $this->client->call($method, $params);

    $json = '{"jsonrpc": "2.0","error": {"code": -32700, "message":"Parse error"}, "id": null}';
    $this->transport->output = $json;

    $this->assertFalse($this->client->batchSend());
    $expects = \JsonRpc\Client::ERR_RPC_RESPONSE;
    $this->assertStringStartsWith($expects, $this->client->error);
    $this->assertNull($this->client->result);
    $this->assertNull($this->client->batch);

  }


  /**
  * Checks that we report an error when batch responses do not match requests sent
  *
  */
  public function testInvalidBatchMissing()
  {

    $method = 'divide';
    $params = array(42, 6);

    $this->client->batchOpen();
    $this->client->call($method, $params);
    $this->client->call($method, $params);
    $this->client->call($method, $params);
    $this->client->call($method, $params);

    $json = '[
      {"jsonrpc": "2.0", "result": 7, "id": 1},
      {"jsonrpc": "2.0", "result": 7, "id": 2},
      {"jsonrpc": "2.0", "result": 7, "id": 3}
    ]';

    $this->transport->output = $json;

    $this->assertFalse($this->client->batchSend());
    $expects = \JsonRpc\Client::ERR_RPC_RESPONSE;
    $this->assertStringStartsWith($expects, $this->client->error);
    $this->assertNull($this->client->result);
    $this->assertNull($this->client->batch);

  }

  /**
  * Checks that an invalid batch response is reported as an error
  * when the ids do not match thoses sent
  *
  */
  public function testInvalidBatchResponseIds()
  {

    $method = 'divide';
    $params = array(42, 6);

    $this->client->batchOpen();
    $this->client->call($method, $params);
    $this->client->call($method, $params);
    $this->client->call($method, $params);
    $this->client->call($method, $params);

    $json = '[
      {"jsonrpc": "2.0", "result": 7, "id": 1},
      {"jsonrpc": "2.0", "result": 7, "id": 2},
      {"jsonrpc": "2.0", "result": 7, "id": 7},
      {"jsonrpc": "2.0", "result": 7, "id": 4}
    ]';

    $this->transport->output = $json;

    $this->assertFalse($this->client->batchSend());
    $expects = \JsonRpc\Client::ERR_RPC_RESPONSE;
    $this->assertStringStartsWith($expects, $this->client->error);
    $this->assertNull($this->client->result);
    $this->assertNull($this->client->batch);

  }


}

