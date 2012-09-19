<?php

  chdir(__DIR__);

  error_reporting(E_ALL);
  date_default_timezone_set('UTC');

  ini_set('display_errors', '0');
  ini_set('log_errors', '1');
  ini_set('error_log', 'server-errors.log');
  ini_set('default_charset', 'UTF-8');

  require('autoload.php');


  # set up our method handler class
  require('ServerMethods.php');

  $methods = new ServerMethods();

  $Server = new JsonRpc\Server($methods);

  try
  {
    $Server->receive();
  }
  catch (Exception $e)
  {
    error_log($e);
  }
