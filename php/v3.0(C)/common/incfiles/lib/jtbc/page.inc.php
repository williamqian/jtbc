<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace jtbc {
  class page
  {
    public static $counter = 0;
    public static $db = null;
    public static $init = false;
    public static $para = array();
    private static $title = array();

    public static function db()
    {
      $db = null;
      if (!is_null(self::$db)) $db = self::$db;
      else
      {
        $db = new db();
        $db -> dbHost = DB_HOST;
        $db -> dbUsername = DB_USERNAME;
        $db -> dbPassword = DB_PASSWORD;
        $db -> dbDatabase = DB_DATABASE;
        $db -> init();
        if ($db -> errStatus != 0) $db = null;
        else self::$db = $db;
      }
      return $db;
    }

    public static function formatResult($argStatus, $argHTML)
    {
      $status = $argStatus;
      $html = $argHTML;
      $html = str_replace(']]>', '&##::~~~::##&', $html);
      $tmpstr = '<?xml version="1.0" encoding="utf-8"?><result status="' . base::getNum($status, 0) . '"><![CDATA[' . $html . ']]></result>';
      return $tmpstr;
    }

    public static function formatXMLResult($argStatus, $argMessage, $argPara = '')
    {
      $status = $argStatus;
      $message = $argMessage;
      $para = $argPara;
      $tmpstr = '<?xml version="1.0" encoding="utf-8"?><result status="' . base::getNum($status, 0) . '" message="' . base::htmlEncode($message) . '" para="' . base::htmlEncode($para) . '"></result>';
      return $tmpstr;
    }

    public static function getHTTPPara($argName, $argType = 'auto')
    {
      $tmpstr = '';
      $name = $argName;
      $type = $argType;
      if ($type == 'auto')
      {
        $tmpstr = base::getString(@$_POST[$name]);
        if (base::isEmpty($tmpstr)) $tmpstr = base::getString(@$_GET[$name]);
      }
      else if ($type == 'post')
      {
        $tmpstr = base::getString(@$_POST[$name]);
      }
      else if ($type == 'get')
      {
        $tmpstr = base::getString(@$_GET[$name]);
      }
      return $tmpstr;
    }

    public static function getPara($argName)
    {
      if (self::$init == false)
      {
        self::$init = true;
        self::init();
      }
      return self::$para[$argName];
    }

    public static function getRemortIP()
    {
      $IPaddress = '';
      if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $IPaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
      else if (isset($_SERVER['HTTP_CLIENT_IP'])) $IPaddress = $_SERVER['HTTP_CLIENT_IP'];
      else $IPaddress = $_SERVER['REMOTE_ADDR'];
      return $IPaddress;
    }

    public static function getTitle()
    {
      $tmpstr = '';
      $title = self::$title;
      if (!empty($title))
      {
        foreach ($title as $key => $val)
        {
          $tmpstr = $val . SEPARATOR . $tmpstr;
        }
      }
      $tmpstr = $tmpstr . tpl::take('global.index.title', 'lng');
      return $tmpstr;
    }

    public static function setTitle($argTitle)
    {
      $title = $argTitle;
      if (!base::isEmpty($title)) array_push(self::$title, $title);
      return self::getTitle();
    }

    public static function replaceQuerystring($argStrers, $argValue = '', $argUrs = '')
    {
      $tmpstr = '';
      $strers = $argStrers;
      $value = $argValue;
      $urs = $argUrs;
      if (base::isEmpty($urs)) $urs = @$_SERVER['QUERY_STRING'];
      if (base::getLeft($urs, 1) == '?') $urs = base::getLRStr($urs, '?', 'rightr');
      $myAry = array();
      if (!base::isEmpty($urs))
      {
        $paraAry = explode('&', $urs);
        foreach ($paraAry as $key => $val)
        {
          $paraItem = trim($val);
          if (!base::isEmpty($paraItem))
          {
            $paraItemAry = explode('=', $paraItem);
            if (count($paraItemAry) == 2) $myAry[$paraItemAry[0]] = $paraItemAry[1];
          }
        }
      }
      if (is_array($strers))
      {
        foreach ($strers as $key => $val) $myAry[$key] = $val;
      }
      else
      {
        $myAry[$strers] = $value;
      }
      foreach ($myAry as $key => $val)
      {
        if (!is_null($val)) $tmpstr .= $key . '=' . $val . '&';
      }
      if (!base::isEmpty($tmpstr)) $tmpstr = base::getLRStr($tmpstr, '&', 'leftr');
      $tmpstr = '?' . $tmpstr;
      return $tmpstr;
    }

    public static function init()
    {
      self::$para['http'] = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
      self::$para['http_host'] = $_SERVER['HTTP_HOST'];
      self::$para['route'] = smart::getRoute();
      self::$para['genre'] = smart::getActualGenre(self::$para['route']);
      self::$para['assetspath'] = ASSETSPATH;
      self::$para['global.assetspath'] = smart::getActualRoute(ASSETSPATH);
      self::$para['folder'] = base::getLRStr($_SERVER['SCRIPT_NAME'], '/', 'leftr') . '/';
      self::$para['filename'] = base::getLRStr($_SERVER['SCRIPT_NAME'], '/', 'right');
      self::$para['uri'] = $_SERVER['SCRIPT_NAME'];
      self::$para['urs'] = $_SERVER['QUERY_STRING'];
      self::$para['url'] = self::$para['uri'];
      self::$para['urlpre'] = self::$para['http'] . self::$para['http_host'];
      if (!base::isEmpty(self::$para['urs'])) self::$para['url'] .= '?' . self::$para['urs'];
    }
  }
}
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
?>