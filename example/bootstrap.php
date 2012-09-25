<?php

spl_autoload_register('autoload');

function autoload($className)
{

  $className = ltrim($className, '\\');
  $fileName  = '';
  $namespace = '';

  if ($lastNsPos = strripos($className, '\\'))
  {
    $namespace = substr($className, 0, $lastNsPos);
    $className = substr($className, $lastNsPos + 1);
    $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
  }

  $fileName .= $className . '.php';

  // set the path to our source directory, relative to the directory we are in
  $src = realpath('..' . DIRECTORY_SEPARATOR . 'src');

  require $src . DIRECTORY_SEPARATOR . $fileName;

}

