<?php
namespace jtbc;
class ui extends page {
  public static function getResult()
  {
    $tmpstr = tpl::take('index.index', 'tpl');
    $tmpstr = tpl::parse($tmpstr);
    return $tmpstr;
  }
}
?>