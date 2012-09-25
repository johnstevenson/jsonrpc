<?php
namespace ServerTests;

class Transport
{

  public $input = '';
  public $output = '';


  public function receive()
  {
    return $this->input;
  }

  public function reply($json)
  {
    $this->output = $json;
  }

}

