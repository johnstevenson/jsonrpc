<?php
namespace JsonRpc\Base;

class Response extends Rpc
{

  protected $result = null;
  protected $error = null;


  public function create($struct)
  {

    $ok = is_array($struct) || is_object($struct);

    if ($ok)
    {
      return $this->init($struct, is_array($struct));
    }
    else
    {
      $this->fault = $this->getErrorMsg('');
    }

  }


  public function createStdError($code, $id = null)
  {
    $this->result = null;
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


  private function init($struct, $new)
  {

    $req = 0;

    try
    {

      #jsonrpc
      $this->setVersion($struct, $new);

      if ($error = $this->get($struct, 'error', static::MODE_GET))
      {
        $this->error = $this->getServerError($error);
        ++ $req;
      }

      if ($this->get($struct, 'result', static::MODE_EXISTS))
      {
        $this->result = $this->get($struct, 'result');
        ++ $req;
      }

      if ($req !== 1)
      {
        $this->fault = $this->getErrorMsg('');
        return;
      }

      $this->id = $this->get($struct, 'id');

      return true;

    }
    catch (\Exception $e)
    {
      $this->fault = $e->getMessage();
    }

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
        $message = 'Server error';
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
      $value = $this->makeError($error);
    }
    else
    {

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

        if ($code && $message)
        {
          $value['code'] = $code;
          $value['message'] = $message;
        }
        elseif ($code)
        {
          $value = $this->makeError($code);
        }
        else
        {
          $value['code'] = $code ?: $value['code'];
          $value['message'] = $message ?: $value['message'];
        }

        if (!empty($error['data']))
        {
          $value['data'] = $error['data'];
        }

      }

    }

    return $this->check('error', $value, true);

  }


}
