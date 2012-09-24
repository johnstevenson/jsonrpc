<?php

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

