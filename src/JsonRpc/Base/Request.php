<?php
namespace JsonRpc\Base;

class Request extends Rpc
{

  protected $method = '';
  protected $params = null;
  protected $notification = false;


  public function __construct($struct)
  {
    $this->init($struct);
  }


  public function toJson()
  {

    $ar['jsonrpc'] = $this->jsonrpc;
    $ar['method'] = $this->method;

    if ($this->notification)
    {

      if ($this->params)
      {
        $ar['params'] = $this->params;
      }

    }
    else
    {
      $ar['params'] = $this->params;
      $ar['id'] = $this->id;
    }

    return json_encode($ar);

  }


  private function init($struct)
  {

    try
    {

      if ($this->get($struct, 'jsonrpc', false))
      {
        $this->jsonrpc = $this->get($struct, 'jsonrpc');
      }

      $this->method = $this->get($struct, 'method');

      if ($this->get($struct, 'params', false))
      {

        $params = $this->get($struct, 'params');

        if (is_object($params))
        {
          $params = (array) $params;
        }

        $this->params = $params;

      }

      if ($this->get($struct, 'id', false))
      {
        $this->id = $this->get($struct, 'id');
      }

      $this->notification = !$this->id;

      return true;

    }
    catch (\Exception $e)
    {
      $this->fault = $e->getMessage();
    }

  }


}
