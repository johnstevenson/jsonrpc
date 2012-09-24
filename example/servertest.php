<?php

  chdir(__DIR__);
  ini_set('default_charset', 'UTF-8');

  # we don't want any PHP errors being output
  ini_set('display_errors', '0');

  # so we will log them. Exception will be logged as well
  ini_set('log_errors', '1');
  ini_set('error_log', 'server-errors.log');

  # autoload for the example directory
  require('autoload.php');

  # set up our method handler class
  require('ServerMethods.php');
  $methods = new ServerMethods();

  # create our server object, passing it the method handler class
  $Server = new JsonRpc\Server($methods);
  $Server->setTransport(new Transport());

  # and tell the server to do its stuff
  $Server->receive();



class Transport
{

  public function receive()
  {

    $data = '[
      {"jsonrpc":"2.0","method":"example","params":["Hello",{"name":"Client"}],"id":1},
      {"jsonrpc":"2.0","method":"example","params":["Hello"]},
      {"jsonrpc":"2.0","method":"example","params":["Hello",{"name":"Client"}],"id":2},
      {"jsonrpc":"2.0","method":"example","params":["Hello",{"name":"Client"}]},
      {"jsonrpc":"2.0","method":"example","params":{"name":"Client"},"id":3}]';

    $data = '{"jsonrpc": "2.0", "method": 1, "params": "bar"}';
    return $data;

  }


  public function reply($json)
  {
    echo $json;
    exit;
  }


}
