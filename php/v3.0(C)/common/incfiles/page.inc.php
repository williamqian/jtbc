<?php
require_once('const.inc.php');
spl_autoload_register(function($argClass){
  $class = $argClass;
  if (!empty($class))
  {
    $file = __DIR__ . '/lib/' . str_replace('\\', '/', $class) . '.inc.php';
    if (is_file($file)) require_once($file);
  }
});
?>