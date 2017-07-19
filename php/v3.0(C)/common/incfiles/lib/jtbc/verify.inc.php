<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace jtbc {
  class verify
  {
    public static function isMobile($argStrers)
    {
      $bool = false;
      $strers = $argStrers;
      if (!base::isEmpty($strers))
      {
        if (preg_match('/^1\d{10}$/', $strers)) $bool = true;
      }
      return $bool;
    }

    public static function isEmail($argStrers)
    {
      $bool = false;
      $strers = $argStrers;
      if (!base::isEmpty($strers))
      {
        if (preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $strers)) $bool = true;
      }
      return $bool;
    }

    public static function isNatural($argStrers)
    {
      $bool = false;
      $strers = $argStrers;
      if (!base::isEmpty($strers))
      {
        if (preg_match('/^[a-zA-Z0-9_-]+$/', $strers)) $bool = true;
      }
      return $bool;
    }
  }
}
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
?>