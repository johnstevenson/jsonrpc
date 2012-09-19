<?php
namespace JsonRpc;

use JsonRpc\Base\Rpc;
use JsonRpc\Base\Request;
use JsonRpc\Base\Response;


class Client
{

  public $result = null;
  public $error = null;
  public $batch = null;

  public $fault = '';
  public $output = '';

  private $uri;
  private $transport = null;
  private $id = 0;
  private $requests = array();
  private $multi = false;
  private $notifications = 0;


  const ERR_RPC_RESPONSE = 'BadRpcResponse';


  public function __construct($uri, $transport = null)
  {

    $this->uri = $uri;
    $this->transport = $transport;

    if (!$this->transport)
    {
      $this->transport = new \JsonRpc\Transport\BasicClient();
    }

  }


  public function call($method, $params)
  {
    return $this->work($method, $params);
  }


  public function notify($method, $params)
  {
    ++ $this->notifications;
    return $this->work($method, $params, true);
  }


  public function batchOpen()
  {
    $this->reset();
    $this->multi = true;
  }


  public function batchSend()
  {

    if (count($this->requests) === 1)
    {
      $this->reset();
      throw new \Exception('Batch only has one request');
      return false;
    }

    $data = '[' . implode(',', $this->requests) . ']';

    return $this->send($data);

  }


  private function work($method, $params, $notify = false)
  {

    $ar = array(
      'method' => $method,
      'params' => is_scalar($params) ? array($params) : $params,
      'id' => $notify ? 0 : ++ $this->id
    );

    $request = new Request($ar);

    if ($request->fault)
    {
      throw new \Exception($request->fault);
      return false;
    }

    $data = $request->toJson();

    if ($this->multi)
    {
      $this->requests[] = $data;
    }
    else
    {
      return $this->send($data);
    }

  }


  private function send($data)
  {

    try
    {

      if ($res = $this->transport->send('POST', $this->uri, $data))
      {
        $this->output = $this->transport->output;
        $res = $this->checkResult();
      }
      else
      {
        $this->fault = $this->transport->error;
      }

      $this->reset();

      return $res;

    }
    catch (\Exception $e)
    {
      $this->fault = $e->getMessage();
    }

  }


  private function checkResult()
  {

    $sent = $this->requests ? count($this->requests) : 1;
    $expected = $sent - $this->notifications;

    if (!$struct = Rpc::decode($this->output, $batch))
    {

      if ($expected)
      {
        $this->fault = static::ERR_RPC_RESPONSE . ': ' .  ' parsing error';
      }

      return !$expected;
    }

    if ($res = $this->checkResponses($struct, $batch))
    {

      if (!$this->checkReceived($struct, $batch, $expected))
      {
        return;
      }



      if ($this->multi)
      {
        $this->batch = $struct;
      }
      else
      {

        if (isset($struct->error))
        {
          $this->error = $struct->error;
        }
        else
        {
          $this->result = $struct->result;
        }

      }

    }

    return $res;

  }


  private function checkResponses($json_array, $batch)
  {

    $res = false;

    if ($batch)
    {

      foreach ($json_array as $item)
      {

        if (!$res = $this->checkResponse($item))
        {
          break;
        }

      }

    }
    else
    {
      $res = $this->checkResponse($json_array);
    }

    return $res;

  }


  private function checkResponse($json_array)
  {

    $response = new Response();

    if (!$res = $response->create($json_array))
    {
      $this->fault = static::ERR_RPC_RESPONSE . ': ' . $response->fault;
    }

    return $res;

  }


  private function checkReceived($struct, $batch, $expected)
  {

    $received = $batch ? count($struct) : 1;

    if ($received !== $expected || $batch !== $this->multi)
    {

      $ok = isset($struct->error) && $struct->error->code === Rpc::ERR_PARSE;

      if ($ok && $received === 1)
      {
        $error = 'Response reports Parse error (' . Rpc::ERR_PARSE . ')';
      }
      else
      {
        $error = 'Mismatched response';
      }

      $this->fault = static::ERR_RPC_RESPONSE . ': ' .  $error;
      return false;

    }
    else
    {
      return true;
    }

  }


  private function reset()
  {

    $this->id = 0;
    $this->requests = array();
    $this->multi = false;
    $this->notifications = 0;

  }


}

