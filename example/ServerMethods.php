<?php

class ServerMethods
{

  public $error = null;


  public function example($msg, $obj)
  {

    $out = 'The server says: ' . $msg . ' ' . $obj->name;
    $date = date(DATE_RFC822);

    # we can set an error with this->error
    //$this->error = 'Invalid params';

    # we can return a string
    //return $out . ' on ' . $date;

    # we can return an object
    $res = new \stdClass();
    $res->reply = $out;
    $res->date = $date;
    return $res;


  }

}
