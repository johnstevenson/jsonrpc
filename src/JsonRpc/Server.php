<?php
namespace JsonRpc;

use JsonRpc\Base\Rpc;
use JsonRpc\Base\Request;
use JsonRpc\Base\Response;


class Server
{

  private $handler;
  private $transport = null;
  private $requests = array();
  private $responses = array();
  private $error = null;


  public function __construct($handler, $transport = null)
  {

    ini_set('display_errors', '0');

    $this->handler = $handler;
    $this->transport = $transport;

    if (!$this->transport)
    {
      $this->transport = new \JsonRpc\Transport\BasicServer();
    }

  }


  public function receive()
  {

    try
    {
      $input = $this->transport->receive();
      $json = $this->process($input);
      $this->transport->reply($json);
    }
    catch (\Exception $e)
    {
      exit;
    }

  }


  private function process($json)
  {

    if (!$struct = Rpc::decode($json, $batch))
    {
      $data = new Response();
      $data->createStdError(Rpc::ERR_PARSE);
      return $data->toJson();
    }

    $this->getRequests($struct);
    $this->processRequests();

    $data = implode(',', $this->responses);

    return $batch && $data ? '[' . $data . ']' : $data;

  }


  private function getRequests($struct)
  {

    if (is_array($struct))
    {

      foreach ($struct as $item)
      {
        $this->requests[] = new Request($item);
      }

    }
    else
    {
     $this->requests[] = new Request($struct);
    }

  }


  private function processRequests()
  {

    foreach ($this->requests as $request)
    {

      if ($request->fault)
      {

        $this->error = array(
          'code' => Rpc::ERR_REQUEST,
          'data' => $request->fault
        );

      }
      else
      {

        try
        {
          $result = $this->processRequest($request->method, $request->params);
        }
        catch (\Exception $e)
        {
          $this->error = Rpc::ERR_INTERNAL;
        }

      }

      if ($request->notification)
      {
        continue;
      }

      $ar = array(
        'id' => $request->id
      );

      if ($this->error)
      {
        $ar['error'] = $this->error;
      }
      else
      {
        $ar['result'] = $result;
      }

      $response = new Response();

      if (!$response->create($ar))
      {
        $response->createStdError(Rpc::ERR_INTERNAL, $request->id);
      }

      $this->responses[] = $response->toJson();

    }

  }


  private function processRequest($method, $params)
  {

    $this->error = null;
    $callback = array($this->handler, $method);

    if (is_callable($callback))
    {

      $ref = new \ReflectionMethod($this->handler, $method);

      $reqArgs = $ref->getNumberOfRequiredParameters();

      if ($reqArgs > count($params))
      {
        $this->error = Rpc::ERR_PARAMS;

        return;
      }

      $result = call_user_func_array($callback, $params);

      if (property_exists($this->handler, 'error') && $this->handler->error)
      {
        $this->error = $this->handler->error;
        $this->handler->error = null;
      }

      return $result;
    }
    else
    {
      $this->error = Rpc::ERR_METHOD;
    }

  }


}
