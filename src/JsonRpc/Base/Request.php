<?php
namespace JsonRpc\Base;

class Request extends Rpc
{

  protected $method = '';
  protected $params = null;
  protected $notification = false;


  public function __construct($struct)
  {

    $ok = is_array($struct) || is_object($struct);

    if ($ok)
    {
      $this->init($struct, is_array($struct));
    }
    else
    {
      $this->fault = $this->getErrorMsg('');
    }

  }


  public function toJson()
  {

    $ar['jsonrpc'] = $this->jsonrpc;
    $ar['method'] = $this->method;

    if ($this->params)
    {
      $ar['params'] = $this->params;
    }

    if (!$this->notification)
    {
      $ar['id'] = $this->id;
    }

    return json_encode($ar);

  }


  private function init($struct, $new)
  {

    try
    {

      if ($this->get($struct, 'id', static::MODE_EXISTS))
      {
        $this->id = $this->get($struct, 'id');
      }
      else
      {
        $this->notification = true;
      }

      #jsonrpc
      $this->setVersion($struct, $new);

      $this->method = $this->get($struct, 'method');

      if ($this->get($struct, 'params', static::MODE_EXISTS))
      {
        $this->params = $this->get($struct, 'params');
      }

      return true;

    }
    catch (\Exception $e)
    {
      $this->fault = $e->getMessage();
    }

  }


}
