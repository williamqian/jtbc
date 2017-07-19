<?php
namespace jtbc;
class ui extends page {
  public static function moduleDefault()
  {
    $tmpstr = tpl::take('index.default', 'tpl');
    $tmpstr = tpl::parse($tmpstr);
    return $tmpstr;
  }
}
?>