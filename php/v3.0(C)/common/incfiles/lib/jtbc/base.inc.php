<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace jtbc {
  class base
  {
    public static function checkIDAry($argStrers)
    {
      $bool = false;
      $strers = $argStrers;
      if (!self::isEmpty($strers))
      {
        $bool = true;
        $arys = explode(',', $strers);
        foreach($arys as $key => $val)
        {
          if (!(is_numeric($val))) $bool = false;
        }
      }
      return $bool;
    }

    public static function checkInstr($argStrers, $argStr, $argSpStr = ',')
    {
      $bool = false;
      $strers = strval($argStrers);
      $str = strval($argStr);
      $spStr = $argSpStr;
      if ($strers == $str) $bool = true;
      else if (is_numeric(strpos($strers, $spStr . $str . $spStr))) $bool = true;
      else if (self::getLRStr($strers, $spStr, 'left') == $str) $bool = true;
      else if (self::getLRStr($strers, $spStr, 'right') == $str) $bool = true;
      return $bool;
    }

    public static function encodeText($argStrers)
    {
      $strers = $argStrers;
      if (!self::isEmpty($strers))
      {
        $strers = str_replace('$', '&#36;', $strers);
        $strers = str_replace('\'', '&#39;', $strers);
        $strers = str_replace('.', '&#46;', $strers);
        $strers = str_replace('@', '&#64;', $strers);
      }
      return $strers;
    }

    public static function encodeTextArea($argStrers)
    {
      $strers = $argStrers;
      if (!self::isEmpty($strers))
      {
        $strers = self::htmlEncode($strers);
        $strers = str_replace(chr(13) . chr(10), chr(10), $strers);
        $strers = str_replace(chr(39), '&#39;', $strers);
        $strers = str_replace(chr(32) . chr(32), '&nbsp; ', $strers);
        $strers = str_replace(chr(10), '<br />', $strers);
      }
      return $strers;
    }

    public static function formatDate($argDate, $argType)
    {
      $tmpstr = '';
      $date = $argDate;
      $type = $argType;
      $date = self::getMKTime($date);
      switch($type)
      {
        case '-3':
          $tmpstr = date('d', $date);
          break;
        case '-2':
          $tmpstr = date('m', $date);
          break;
        case '-1':
          $tmpstr = date('Y', $date);
          break;
        case '1':
          $tmpstr = date('Y-m-d', $date);
          break;
        case '2':
          $tmpstr = date('Y.m.d', $date);
          break;
        case '10':
          $tmpstr = date('Ymd', $date);
          break;
        case '11':
          $tmpstr = date('His', $date);
          break;
        case '20':
          $tmpstr = date('m-d H:i', $date);
          break;
        case '100':
          $tmpstr = date('Y-m-d H:i:s', $date);
          break;
        default:
          $tmpstr = date('Y-m-d H:i:s', $date);
          break;
      }
      return $tmpstr;
    }

    public static function formatFileSize($argSize)
    {
      $tmpstr = '';
      $size = self::getNum($argSize, 0);
      if ($size == 0) $tmpstr = '0B';
      else
      {
        $sizename = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $tmpstr = round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . @$sizename[$i];
      }
      return $tmpstr;
    }

    public static function formatLine($argContent, $argTPL)
    {
      $tmpstr = '';
      $content = $argContent;
      $tpl = $argTPL;
      if (!self::isEmpty($content))
      {
        $content = self::htmlEncode($content);
        $content = str_replace(chr(13) . chr(10), chr(10), $content);
        $contentAry = explode(chr(10), $content);
        foreach ($contentAry as $key => $val)
        {
          if (!self::isEmpty($val))
          {
            $tmpstr .= str_replace('$line', $val, $tpl);
          }
        }
      }
      return $tmpstr;
    }

    public static function getDateTime($argDateTime = '')
    {
      $tmpstr = '';
      $dateTime = $argDateTime;
      if (self::isEmpty($dateTime) || !self::isDate($dateTime))
      {
        $tmpstr = date('Y-m-d H:i:s', time());
      }
      else $tmpstr = $dateTime;
      return $tmpstr;
    }

    public static function getFolderInfo($argPath)
    {
      $path = $argPath;
      $dir = @dir($path);
      $size = 0;
      $folder = 0;
      $file = 0;
      while($entry = $dir -> read())
      {
        if ($entry != '.' && $entry != '..')
        {
          if (is_dir($path . $entry))
          {
            $folder += 1;
            $info = self::getFolderInfo($path . $entry . '/');
            if (is_array($info))
            {
              $folder += $info['folder'];
              $file += $info['file'];
              $size += $info['size'];
            }
          }
          else if (is_file($path . $entry))
          {
            $file += 1;
            $size += filesize($path . $entry);
          }
        }
      }
      $info = array('size' => $size, 'folder' => $folder, 'file' => $file);
      return $info;
    }

    public static function getFileGroup($argFileType)
    {
      $filegroup = 0;
      $fileType = $argFileType;
      if ($fileType == 'jpg' || $fileType == 'jpeg' || $fileType == 'gif' || $fileType == 'png') $filegroup = 1;
      else if ($fileType == 'mp4' || $fileType == 'm4a') $filegroup = 2;
      else if ($fileType == 'doc' || $fileType == 'docx' || $fileType == 'xls' || $fileType == 'xlsx' || $fileType == 'ppt' || $fileType == 'pptx' || $fileType == 'pdf') $filegroup = 3;
      return $filegroup;
    }

    public static function getLeft($argStrers, $argLen)
    {
      $strers = $argStrers;
      $len = $argLen;
      $tmpstr = mb_substr($strers, 0, $len, CHARSET);
      return $tmpstr;
    }

    public static function getLeftB($argStrers, $argLen, $argEllipsis)
    {
      $tmpstr = '';
      $len = $argLen;
      $strers = $argStrers;
      $ellipsis = $argEllipsis;
      $tmpstr = mb_strcut($strers, 0, $len * 3, CHARSET);
      if ($tmpstr != $strers) $tmpstr = $tmpstr . $ellipsis;
      return $tmpstr;
    }

    public static function getLRStr($argString, $argSpStr, $argType)
    {
      $tmpstr = '';
      $string = $argString;
      $spStr = $argSpStr;
      $type = $argType;
      if (self::isEmpty($spStr) || !(is_numeric(strpos($string, $spStr)))) $tmpstr = $string;
      else
      {
        switch($type)
        {
          case 'left':
            $tmpstr = substr($string, 0, strpos($string, $spStr));
            break;
          case 'leftr':
            $tmpstr = substr($string, 0, strrpos($string, $spStr));
            break;
          case 'right':
            $index = 0 - (strlen($string) - strrpos($string, $spStr) - strlen($spStr));
            if ($index != 0) $tmpstr = substr($string, $index);
            break;
          case 'rightr':
            $index = 0 - (strlen($string) - strpos($string, $spStr) - strlen($spStr));
            if ($index != 0) $tmpstr = substr($string, $index);
            break;
          default:
            $tmpstr = $string;
            break;
        }
      }
      return $tmpstr;
    }

    public static function getMKTime($argDate)
    {
      $mkTime = 0;
      $date = $argDate;
      if (self::isDate($date))
      {
        $arys = explode(' ', $date);
        $arys2 = explode('-', $arys[0]);
        $arys3 = explode(':', $arys[1]);
        $month = self::getNum($arys2[1], 0);
        $day = self::getNum($arys2[2], 0);
        $year = self::getNum($arys2[0], 0);
        $hour = 0;
        $minute = 0;
        $second = 0;
        if (count($arys3) == 3)
        {
          $hour = self::getNum($arys3[0], 0);
          $minute = self::getNum($arys3[1], 0);
          $second = self::getNum($arys3[2], 0);
        }
        $mkTime = mktime($hour, $minute, $second, $month, $day, $year);
      }
      return $mkTime;
    }

    public static function getNum($argNumber, $argDefault = 0)
    {
      $num = 0;
      $number = $argNumber;
      $default = $argDefault;
      if (is_numeric($number))
      {
        if (is_numeric(strpos($number, '.'))) $num = doubleval($number);
        else $num = intval($number);
      }
      else $num = $default;
      return $num;
    }

    public static function getParameter($argStrers, $argStr, $argSpStr = ';')
    {
      $tmpstr = '';
      $str = $argStr;
      $spStr = $argSpStr;
      $strers = $argStrers;
      $regMatch = preg_match('((?:^|' . $spStr . ')' . $str . '=(.[^' . $spStr . ']*))', $strers, $regArys);
      if (count($regArys) == 2) $tmpstr = $regArys[1];
      return $tmpstr;
    }

    public static function getRandomString($argLength)
    {
      $tmpstr = '';
      $length = $argLength;
      $chars = 'abcdefghijklmnopqrstuvwxyz1234567890';
      $max = strlen($chars) - 1;
      for($i = 0; $i < $length; $i++)
      {
        $tmpstr .= $chars[rand(0, $max)];
      }
      return $tmpstr;
    }

    public static function getRepeatedString($argString, $argNum)
    {
      $tmpstr = '';
      $string = $argString;
      $num = self::getNum($argNum);
      for ($ti = 0; $ti < $num; $ti ++) $tmpstr .= $string;
      return $tmpstr;
    }

    public static function getRight($argStrers, $argLen)
    {
      $strers = $argStrers;
      $len = $argLen;
      $tmpstr = mb_substr($strers, (mb_strlen($strers, CHARSET) - $len), $len, CHARSET);
      return $tmpstr;
    }

    public static function getString($argStrers)
    {
      $strers = $argStrers;
      if (is_numeric($strers)) $strers = strval($strers);
      if ($strers == null) $strers = '';
      return $strers;
    }

    public static function getSwapString($argString1, $argString2)
    {
      $tmpstr = '';
      $string1 = $argString1;
      $string2 = $argString2;
      $tmpstr = $string1;
      if (self::isEmpty($tmpstr)) $tmpstr = $string2;
      return $tmpstr;
    }

    public static function htmlEncode($argStrers)
    {
      $strers = $argStrers;
      if (!self::isEmpty($strers))
      {
        $strers = str_replace('&', '&amp;', $strers);
        $strers = str_replace('>', '&gt;', $strers);
        $strers = str_replace('<', '&lt;', $strers);
        $strers = str_replace('"', '&quot;', $strers);
        $strers = self::encodeText($strers);
      }
      return $strers;
    }

    public static function isEmpty($argString)
    {
      $bool = false;
      $string = $argString;
      if (trim($string) == '') $bool = true;
      return $bool;
    }

    public static function isDate($argDate)
    {
      $bool = false;
      $date = $argDate;
      $arys = explode(' ', $date);
      if (count($arys) == 2)
      {
        $arys2 = explode('-', $arys[0]);
        $arys3 = explode(':', $arys[1]);
        if (count($arys2) == 3 && count($arys3) == 3) $bool = true;
      }
      else
      {
        $arys2 = explode('-', $arys[0]);
        if (count($arys2) == 3) $bool = true;
      }
      return $bool;
    }

    public static function isImage($argExtension)
    {
      $bool = false;
      $extension = $argExtension;
      if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'png') $bool = true;
      return $bool;
    }

    public static function isImageFormat($argFilepath)
    {
      $bool = false;
      $filepath = $argFilepath;
      if (is_file($filepath))
      {
        $file = fopen($filepath, 'rb');
        $head = fread($file, 0x400);
        fclose($file);
        if (substr($head, 0, 3) == "\xFF\xD8\xFF") $bool = true;
        else if (substr($head, 0, 4) == 'GIF8') $bool = true;
        else if (substr($head, 0, 8) == "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A") $bool = true;
      }
      return $bool;
    }

    public static function isMobileAgent()
    {
      $bool = false;
      $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
      if (strpos($userAgent, 'android') && strpos($userAgent, 'mobile')) $bool = true;
      else if (strpos($userAgent, 'iphone')) $bool = true;
      else if (strpos($userAgent, 'ipod')) $bool = true;
      return $bool;
    }

    public static function mergeIdAry($argIdAry1, $argIdAry2)
    {
      $tmpstr = '';
      $ary1 = $argIdAry1;
      $ary2 = $argIdAry2;
      if (self::checkIDAry($ary1) && self::checkIDAry($ary2)) $tmpstr = $ary1 . ',' . $ary2;
      else if (!self::checkIDAry($ary1) && self::checkIDAry($ary2)) $tmpstr = $ary2;
      else if (self::checkIDAry($ary1) && !self::checkIDAry($ary2)) $tmpstr = $ary1;
      return $tmpstr;
    }

    public static function removeDir($argDir)
    {
      $bool = false;
      $dir = $argDir;
      $dirs = opendir($dir);
      while ($file = readdir($dirs))
      {
        if($file != '.' && $file != '..')
        {
          $repath = $dir . '/' . $file;
          if(!is_dir($repath)) @unlink($repath);
          else self::removeDir($repath);
        }
      }
      closedir($dirs);
      if(@rmdir($dir)) $bool = true;
      return $bool;
    }
  }
}
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
?>