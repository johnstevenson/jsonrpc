<?php
namespace JsonRpc\Transport;


class BasicServer
{

  public function receive()
  {

    return @file_get_contents('php://input');

  }


  public function reply($json)
  {
    echo $json;
    exit;
  }


}

