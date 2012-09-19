<?php
namespace JsonRpc\Base;

class Response extends Rpc
{

  protected $result = null;
  protected $error = null;


  public function create($struct)
  {

    $this->reset();

    try
    {

      if ($this->get($struct, 'jsonrpc', false))
      {
        $this->jsonrpc = $this->get($struct, 'jsonrpc');
      }

      if ($error = $this->get($struct, 'error', false))
      {
        $this->error = $this->getServerError($error);
      }
      else
      {
        $this->result = $this->get($struct, 'result');
      }

      $this->id = $this->get($struct, 'id');

      return true;

    }
    catch (\Exception $e)
    {
      $this->fault = $e->getMessage();
    }

  }


  public function createStdError($code, $id = null)
  {

    $this->reset();
    $this->error = $this->makeError($code);
    $this->id = $id;

  }


  public function toJson()
  {

    $ar['jsonrpc'] = $this->jsonrpc;

    if ($this->error)
    {
      $ar['error'] = $this->error;
    }
    else
    {
      $ar['result'] = $this->result;
    }

    $ar['id'] = $this->id;

    return json_encode($ar);

  }


  private function makeError($code)
  {

    switch ($code)
    {

      case static::ERR_PARSE:
        $message = 'Parse error';
        break;

      case static::ERR_REQUEST:
        $message = 'Invalid Request';
        break;

      case static::ERR_METHOD:
        $message = 'Method not found';
        break;

      case static::ERR_PARAMS:
        $message = 'Invalid params';
        break;

      case static::ERR_INTERNAL:
        $message = 'Internal error';
        break;

      default:
        $code = static::ERR_SERVER;
        $message = 'Server Error';
        break;

    }

    return array(
      'code' => $code,
      'message' => $message,
    );

  }


  private function getServerError($error)
  {

    if (is_int($error))
    {
      return $this->makeError($error);
    }

    $value = $this->makeError(static::ERR_SERVER);

    if (is_scalar($error))
    {
      $value['data'] = $error;
    }
    else
    {

      if (!is_array($error))
      {
        $error = (array) $error;
      }

      $code = !empty($error['code']) ? $error['code'] : null;
      $message = !empty($error['message']) ? $error['message'] : null;

      $value['code'] = $code ?: $value['code'];
      $value['message'] = $message ?: $value['message'];

      if (!empty($error['data']))
      {
        $value['data'] = $error['data'];
      }

    }

    return $this->check('error', $value);

  }


  private function reset()
  {
    $this->id = null;
    $this->result = null;
    $this->error = null;
  }


}
