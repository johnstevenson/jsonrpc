<?php
namespace JsonRpc;

use JsonRpc\Base\Rpc;
use JsonRpc\Base\Request;
use JsonRpc\Base\Response;


class Server
{

  private $handler;
  private $transport = null;
  private $logger = null;
  private $assoc = false;
  private $requests = array();
  private $responses = array();
  private $error = null;
  private $handlerError = null;
  private $refClass = null;


  public function __construct($methodHandler, $transport = null)
  {

    ini_set('display_errors', '0');

    $this->handler = $methodHandler;
    $this->transport = $transport;

    if (!$this->transport)
    {
      $this->transport = new Transport\BasicServer();
    }

  }


  public function receive($input = '')
  {

    $this->init();

    try
    {
      $input = $input ?: $this->transport->receive();
      $json = $this->process($input);
      $this->transport->reply($json);
    }
    catch (\Exception $e)
    {
      $this->logException($e);
      exit;
    }

  }


  public function setTransport($transport)
  {
    $this->transport = $transport;
  }


  public function setLogger($logger)
  {
    $this->logger = $logger;
  }


  public function setObjectsAsArrays()
  {
    $this->assoc = true;
  }


  private function process($json)
  {

    if (!$struct = Rpc::decode($json, $batch))
    {
      $code = is_null($struct) ? Rpc::ERR_PARSE : Rpc::ERR_REQUEST;
      $data = new Response();
      $data->createStdError($code);
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

      # check if we got an error parsing the request, otherwise process it
      if ($request->fault)
      {

        $this->error = array(
          'code' => Rpc::ERR_REQUEST,
          'data' => $request->fault
        );

        # we always response to request errors
        $this->addResponse($request, null);

        continue;

      }

      $result = $this->processRequest($request->method, $request->params);

      if (!$request->notification)
      {
        $this->addResponse($request, $result);
      }

    }

  }


  private function processRequest($method, $params)
  {

    $this->error = null;

    if (!$callback = $this->getCallback($method))
    {
      $this->error = Rpc::ERR_METHOD;

      return;
    }

    if (!$this->checkMethod($method, $params))
    {
      $this->error = Rpc::ERR_PARAMS;

      return;
    }

    if ($this->assoc)
    {
      $this->castObjectsToArrays($params);
    }

    try
    {
      $result = call_user_func_array($callback, $params);
    }
    catch (\Exception $e)
    {
      $this->logException($e);
      $this->error = Rpc::ERR_INTERNAL;

      return;
    }

    if ($this->error = $this->getHandlerError())
    {
      $this->clearHandlerError();
    }

    return $result;

  }


  private function addResponse($request, $result)
  {

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
      $this->logError($response->fault);
      $response->createStdError(Rpc::ERR_INTERNAL, $request->id);
    }

    $this->responses[] = $response->toJson();

  }


  private function getCallback($method)
  {

    $callback = array($this->handler, $method);

    if (is_callable($callback))
    {
      return $callback;
    }

  }


  private function checkMethod($method, &$params)
  {

    try
    {

      if (!$this->refClass)
      {
        # we have already checked that handler is callable
        $this->refClass = new \ReflectionClass($this->handler);

        try
        {

          $prop = $this->refClass->getProperty('error');

          if ($prop->isPublic())
          {
            $this->handlerError = $prop;
          }

        }
        catch (\Exception $e){}

      }

      try
      {
        $refMethod = $this->refClass->getMethod($method);
      }
      catch (\Exception $e)
      {
        # we know we are callable, so the class must be implementing __call or __callStatic
        $params = $this->getParams($params);
        return true;
      }

      $res = true;

      if (is_object($params))
      {

        $named = (array) $params;
        $params = array();
        $refParams = $refMethod->getParameters();

        foreach ($refParams as $arg)
        {

          $argName = $arg->getName();

          if (array_key_exists($argName, $named))
          {
            $params[] = $named[$argName];
            unset($named[$argName]);
          }
          elseif (!$arg->isOptional())
          {
            $res = false;
            break;
          }

        }

        if ($extra = array_values($named))
        {
          $params = array_merge($params, $extra);
        }

      }
      else
      {
        $params = $this->getParams($params);
        $reqArgs = $refMethod->getNumberOfRequiredParameters();
        $res = count($params) >= $reqArgs;
      }

      return $res;

    }
    catch (\Exception $e) {}

  }


  private function getParams($params)
  {

    if (is_object($params))
    {
      $params = array_values((array) $params);
    }
    elseif (is_null($params))
    {
      $params = array();
    }

    return $params;

  }


  private function castObjectsToArrays(&$params)
  {

    foreach ($params as &$param)
    {

      if (is_object($param))
      {
        $param = (array) $param;
      }

    }

  }


  private function getHandlerError()
  {

    if ($this->handlerError)
    {

      if ($this->handlerError->isStatic())
      {
        return $this->handlerError->getValue();
      }
      else
      {
        return $this->handlerError->getValue($this->handler);
      }

    }

  }

  private function clearHandlerError()
  {

    if ($this->handlerError)
    {

      if ($this->handlerError->isStatic())
      {
        return $this->handlerError->setValue(null);
      }
      else
      {
        return $this->handlerError->setValue($this->handler, null);
      }

    }

  }

  private function logException(\Exception $e)
  {
    $message = 'Exception: '. $e->getMessage();
    $message .= ' in ' . $e->getFile() . ' on line ' . $e->getLine();
    $this->logError($message);
  }

  private function logError($message)
  {

    try
    {

      if ($this->logger)
      {

        $callback = array($this->logger, 'addRecord');

        $params = array(
          500,
          $message
        );

        $result = call_user_func_array($callback, $params);

      }
      else
      {
        error_log($message);
      }

    }
    catch (\Exception $e)
    {
      error_log($e->__toString());
    }

  }

  private function init()
  {
    $this->requests = array();
    $this->responses = array();
    $this->error = null;
    $this->handlerError = null;
    $this->refClass = null;
  }


}
