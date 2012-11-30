<?php

DEFINE('TEST_DIVIDE_ERROR', 'Cannot divide by zero');

class Helpers
{

  public static function fmt($json)
  {
    return preg_replace('/\\s+/', '', $json);
  }

  public static function addErrors($expects, $errorData)
  {

    $obj = json_decode($expects);

    $batch = is_array($obj);
    $count = 0;

    if (!$batch)
    {
      $obj = array($obj);
    }

    if (!is_array($errorData))
    {
      $errorData = array($errorData);
    }

    foreach ($obj as $request)
    {

      if (property_exists($request, 'error'))
      {

        if ($error = array_shift($errorData))
        {
          ++ $count;
          $request->error->data = $error;
        }

      }

    }

    if (!$count)
    {
      throw new \Exception('No errors added');
    }

    if (!$batch)
    {
      $obj = $obj[0];
    }

    return json_encode($obj);

  }

}


class MethodsClass
{

  public $error = null;


  public function divide($dividend, $divisor, $int = false)
  {

    if (!$divisor)
    {
      $this->error = TEST_DIVIDE_ERROR;
    }
    else
    {
      $quotient = $dividend / $divisor;
      return $int ? (int) $quotient : $quotient;
    }

  }


  public function ping($msg, $user)
  {

    $res = new \stdClass();

    $details = is_array($user) ? $user : (array) $user;
    $res->reply = $msg . ' ' . $details['name'] . ' (' . $details['id'] . ')';
    $res->class = get_class();
    $res->type = gettype($user);

    return $res;

  }

}


class MethodsClassCall
{

  public $error = null;


  public function __call($method, $params)
  {

    if (method_exists('MethodsStatic', $method))
    {

      try
      {
        $res = call_user_func_array(array('MethodsStatic', $method), $params);
        $this->error = MethodsStatic::$error;
        return $res;
      }
      catch (Exception $e)
      {
        $this->error = -32603;
      }
    }

  }

}


class MethodsStatic
{

  public static $error = null;


  public static function divide($dividend, $divisor, $int = false)
  {

    if (!$divisor)
    {
      static::$error = TEST_DIVIDE_ERROR;
    }
    else
    {
      $quotient = $dividend / $divisor;
      return $int ? (int) $quotient : $quotient;
    }

  }

  public static function ping($msg, $user)
  {

    $res = new \stdClass();

    $details = is_array($user) ? $user : (array) $user;
    $res->reply = $msg . ' ' . $details['name'] . ' (' . $details['id'] . ')';
    $res->class = get_class();
    $res->type = gettype($user);

    return $res;

  }

}

class MethodsException
{

  public static function divide($dividend, $divisor, $int = false)
  {
    throw new Exception(__FUNCTION__);
  }

  public static function ping($msg, $user)
  {
    throw new Exception(__FUNCTION__);
  }

}

class ServerLogger
{

  public $level;
  public $message;

  public function addRecord($level, $message, array $context = array())
  {
    $this->level = $level;
    $this->message = $message;
  }

}



/**
* Dummy functions for testing
*/
function divide(){}
function ping(){}

function methodsJsonRpcClosure()
{
  return function (){};
}
