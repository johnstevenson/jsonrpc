<?php
namespace JsonRpc\Base;


class Rpc
{

  public $fault = '';

  protected $jsonrpc = '2.0';
  protected $id = null;

  const ERR_PARSE = -32700;
  const ERR_REQUEST = -32600;
  const ERR_METHOD = -32601;
  const ERR_PARAMS = -32602;
  const ERR_INTERNAL = -32603;
  const ERR_SERVER = -32000;


  public static function decode($message, &$batch)
  {

    $struct = @json_decode($message, false);
    $batch = is_array($struct);

    return $struct;

  }


  public function __get($name)
  {

    if (isset($this->$name))
    {
      return $this->$name;
    }

  }


  protected function check($name, $value)
  {

    switch ($name)
    {

      case 'jsonrpc':
        $res = $value === $this->jsonrpc;
        break;

      case 'method':
        $res = is_string($value) && $value;
        break;

      case 'params':
        $res = is_array($value) || is_object($value);
        break;

      case 'id':
        $res = $this->checkId($value);
        break;

      case 'result':
        $res = true;
        break;

      case 'error':
        $res = $this->checkError($value);
        break;

      default:
        $res = false;

    }

    if (!$res)
    {
      throw new \Exception('Invalid value for: ' . $name);
    }
    else
    {
      return $value;
    }

  }


  protected function get($container, $key, $check = true)
  {

    if (is_array($container))
    {
      $value = isset($container[$key]) ? $container[$key] : null;
    }
    else if (is_object($container))
    {
      $value = isset($container->$key) ? $container->$key : null;
    }

    else
    {
      # set to unknown to trigger an error
      $key = 'unknown';
      $value = null;
    }

    if ($check)
    {
      return $this->check($key, $value);
    }
    else
    {
      return $value;
    }

  }


  private function checkError($error)
  {

    if (!is_array($error))
    {
      $error = (array) $error;
    }

    $code = isset($error['code']) ? $error['code'] : null;
    $message = isset($error['message']) ? $error['message'] : null;
    return is_int($code) && $code && is_string($message);

  }


  private function checkId($id)
  {

    $allowNull = false;

    if (isset($this->error))
    {
      $errorCode = $this->get($this->error, 'code', false);
      $allowNull = $errorCode === static::ERR_PARSE;
    }

    if (!$res = is_string($id) || is_int($id))
    {
      $res = $allowNull && is_null($id);
    }

    return $res;

  }


}
