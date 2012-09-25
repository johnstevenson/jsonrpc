<?php

DEFINE('TEST_DIVIDE_ERROR', 'Cannot divide by zero');

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
    $res->reply = $msg . ' ' . $user->name . ' (' . $user->id . ')';
    $res->class = get_class();
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
      $res = call_user_func_array(array('MethodsStatic', $method), $params);
      $this->error = MethodsStatic::$error;
      return $res;
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
    $res->reply = $msg . ' ' . $user->name . ' (' . $user->id . ')';
    $res->class = get_class();
    return $res;
  }

}

