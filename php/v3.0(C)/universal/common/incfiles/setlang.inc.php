<?php
namespace jtbc;
class ui extends page {
  public static function getRedirect()
  {
    $backurl = request::getHTTPPara('backurl');
    $language = request::getHTTPPara('language');
    if (base::isEmpty($backurl)) $backurl = smart::getActualRoute('./', 1);
    $lang = base::getNum(tpl::take('global.config.lang-' . $language, 'cfg'), -1);
    if ($lang != -1)
    {
      setcookie(APPNAME . 'config[language]', $language, time() + 31536000, COOKIESPATH);
    }
    return $backurl;
  }
}
?>