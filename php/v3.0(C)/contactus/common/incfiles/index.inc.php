<?php
namespace jtbc;
class ui extends page {
  public static function getResult()
  {
    self::setTitle(tpl::take('index.title', 'lng'));
    $tmpstr = tpl::take('index.index', 'tpl');
    $tmpstr = tpl::parse($tmpstr);
    return $tmpstr;
  }
}
?>