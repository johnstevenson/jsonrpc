<?php

  chdir(__DIR__);

  error_reporting(E_ALL);
  date_default_timezone_set('UTC');

  ini_set('display_errors', '1');
  ini_set('log_errors', '1');
  ini_set('error_log', 'client-errors.log');
  ini_set('default_charset', 'UTF-8');

  require('autoload.php');

  $url = getDemoUrl();

  $Client = new JsonRpc\Client($url);

  $method = 'example';

  $param1 = 'Hello';

  $param2 = new \stdClass();
  $param2->name = 'Client';

  $res = $Client->call($method, array($param1, $param2));
  //$res = $Client->notify($method, $params);

  /*
  $Client->batchOpen();
  $Client->call($method, $params);
  $Client->notify($method, $params);
  $Client->call($method, $params);
  $Client->notify($method, $params);
  $Client->call($method, $params);
  $res = $Client->batchSend();
  */

  echo '<form method="GET">';
  echo '<input type="submit" value="Run Example">';
  echo '</form>';
  echo '<pre>';

  echo '<b>return:</b> ';
  echo $res ? 'true' : 'false';
  echo '<br /><br />';

  if (!$res)
  {
    echo '<b>fault:</b> ', $Client->fault;
    echo '<br /><br />';
  }

  if ($Client->error)
  {
    echo '<b>error:</b> ', print_r($Client->error, 1);
    echo '<br /><br />';
  }
  elseif ($Client->result)
  {
    echo '<b>result:</b> ', print_r($Client->result, 1);
    echo '<br /><br />';
  }
  elseif ($Client->batch)
  {
    echo '<b>batch:</b> ', print_r($Client->batch, 1);
    echo '<br /><br />';
  }

  echo '<b>output:</b> ', $Client->output;


function getDemoUrl()
{

  $path = dirname($_SERVER['PHP_SELF']) . '/server.php';
  $scheme = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') ? 'https' : 'http';
  return $scheme . '://' . $_SERVER['HTTP_HOST'] . $path;

}
