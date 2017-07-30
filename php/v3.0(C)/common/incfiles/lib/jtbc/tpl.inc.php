<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace jtbc {
  use DOMXPath;
  use DOMDocument;
  class tpl
  {
    public $tplString;
    public $tplAString = '';
    public $tplRString = '';
    private $tplCString = '<!--JTBC_CINFO-->';

    public function changeTemplate(&$templatestr, $argDistinstr)
    {
      $tmpstr = '';
      $distinstr = $argDistinstr;
      if (is_numeric(strpos($templatestr, $distinstr)))
      {
        $arys = explode($distinstr, $templatestr);
        if (count($arys) == 3)
        {
          $templatestr = $arys[0] . $this -> tplCString . $arys[2];
          $tmpstr = $arys[1];
        }
      }
      return $tmpstr;
    }

    public function getLoopString($argTag)
    {
      $tag = $argTag;
      $this -> tplAString = $this -> changeTemplate($this -> tplString, $tag);
      $tmpstr = $this -> tplAString;
      return $tmpstr;
    }

    public function insertLoopLine($argString)
    {
      $string = $argString;
      $this -> tplRString .= $string;
    }

    public function mergeTemplate()
    {
      $this -> tplString = str_replace($this -> tplCString, $this -> tplRString, $this -> tplString);
      $tmpstr = $this -> tplString;
      return $tmpstr;
    }

    public function strReplace($argString1, $argString2)
    {
      $string1 = $argString1;
      $string2 = $argString2;
      $tmpstr = $this -> tplString;
      $tmpstr = str_replace($string1, $string2, $tmpstr);
      $this -> tplString = $tmpstr;
    }

    public static function bring($argCodeName, $argType = 'tpl', $argValue = '', $argNodeName = null)
    {
      $bool = false;
      $type = $argType;
      $codename = $argCodeName;
      $value = $argValue;
      $nodeName = $argNodeName;
      $codename = self::getAbbrTransKey($codename);
      $routeStr = self::getXMLRoute($codename, $type);
      $key = base::getLRStr($codename, '.', 'right');
      $activeValue = self::getActiveValue($type);
      if (!base::isEmpty($nodeName)) $activeValue = $nodeName;
      $bool = self::setXMLInfo($routeStr, $activeValue, $key, $value);
      return $bool;
    }

    public static function getActiveValue($argType)
    {
      $tmpstr = '';
      $key = '';
      $type = $argType;
      switch($type)
      {
        case 'cfg':
          $key = 'language';
          break;
        case 'lng':
          $key = 'language';
          break;
        case 'sel':
          $key = 'language';
          break;
        case 'tpl':
          $key = 'template';
          break;
      }
      if (!base::isEmpty($key))
      {
        if ($key == 'language') $tmpstr = LANGUAGE;
        else if ($key == 'template') $tmpstr = TEMPLATE;
        $cookieValue = base::getString(@$_COOKIE[APPNAME . 'config'][$key]);
        if (!base::isEmpty($cookieValue)) $tmpstr = $cookieValue;
      }
      return $tmpstr;
    }

    public static function getAbbrTransKey($argCodeName)
    {
      $codename = $argCodeName;
      if (!base::isEmpty($codename))
      {
        if (substr($codename, 0, 1) == '.') $codename = 'global.' . base::getLRStr($codename, '.', 'rightr');
        else if (substr($codename, 0, 2) == '::') $codename = 'global.' . CONSOLEDIR . ':' . base::getLRStr($codename, '::', 'right');
        else if (substr($codename, 0, 2) == ':/') $codename = 'global.' . CONSOLEDIR . '/' . base::getLRStr($codename, ':/', 'right');
      }
      return $codename;
    }

    public static function getEvalValue($argString)
    {
      $tstr = '';
      $string = $argString;
      $ns = __NAMESPACE__;
      if (!base::isEmpty($string))
      {
        if (substr($string, 0 ,1) == '$')
        {
          $string = substr($string, 1, strlen($string) - 1);
          $tstr = page::getPara($string);
        }
        else if (substr($string, 0 ,1) == '#')
        {
          $string = substr($string, 1, strlen($string) - 1);
          @eval('$tstr = $GLOBALS[' . $string . '];');
        }
        else
        {
          if (is_numeric(strpos($string, '(')))
          {
            if (is_numeric(strpos($string, '$')))
            {
              $regm = preg_match_all('(\$(.[^\(]*)\()', $string, $innerFun);
              if ($regm)
              {
                for ($i = 0; $i <= count($innerFun[0]) - 1; $i ++)
                {
                  $funName = $innerFun[1][$i];
                  if (!function_exists($funName))
                  {
                    if (method_exists($ns . '\\page', $funName)) $string = str_replace('$' . $funName, $ns . '\\page::' . $funName, $string);
                    else if (method_exists($ns . '\\request', $funName)) $string = str_replace('$' . $funName, $ns . '\\request::' . $funName, $string);
                    else if (method_exists($ns . '\\base', $funName)) $string = str_replace('$' . $funName, $ns . '\\base::' . $funName, $string);
                    else if (method_exists($ns . '\\tpl', $funName)) $string = str_replace('$' . $funName, $ns . '\\tpl::' . $funName, $string);
                    else if (method_exists($ns . '\\smart', $funName)) $string = str_replace('$' . $funName, $ns . '\\smart::' . $funName, $string);
                  }
                }
              }
            }
            $fun = base::getLRStr($string, '(', 'left');
            if (function_exists($fun)) eval('$tstr = ' . $string . ';');
            else
            {
              if (method_exists($ns . '\\page', $fun)) eval('$tstr = ' . $ns . '\\page::' . $string . ';');
              else if (method_exists($ns . '\\request', $fun)) eval('$tstr = ' . $ns . '\\request::' . $string . ';');
              else if (method_exists($ns . '\\base', $fun)) eval('$tstr = ' . $ns . '\\base::' . $string . ';');
              else if (method_exists($ns . '\\tpl', $fun)) eval('$tstr = ' . $ns . '\\tpl::' . $string . ';');
              else if (method_exists($ns . '\\smart', $fun)) eval('$tstr = ' . $ns . '\\smart::' . $string . ';');
            }
          }
          else eval('$tstr = ' . $string . ';');
        }
      }
      return $tstr;
    }

    public static function getXRootAtt($argSourcefile, $argAtt)
    {
      $sourceFile = $argSourcefile;
      $att = $argAtt;
      $rests = null;
      if (is_file($sourceFile))
      {
        $doc = new DOMDocument();
        $doc -> load($sourceFile);
        $xpath = new DOMXPath($doc);
        $query = '//xml';
        $rests = $xpath -> query($query) -> item(0) -> getAttribute($att);
      }
      return $rests;
    }

    public static function getXMLInfo($argSourcefile, $argKeyword)
    {
      $keyword = $argKeyword;
      $sourceFile = $argSourcefile;
      $karys = array();
      if (is_file($sourceFile))
      {
        $doc = new DOMDocument();
        $doc -> load($sourceFile);
        $xpath = new DOMXPath($doc);
        $query = '//xml/configure/node';
        $node = $xpath -> query($query) -> item(0) -> nodeValue;
        $query = '//xml/configure/field';
        $field = $xpath -> query($query) -> item(0) -> nodeValue;
        $query = '//xml/configure/base';
        $base = $xpath -> query($query) -> item(0) -> nodeValue;
        $fieldArys = explode(',', $field);
        $tki = 0;
        for ($i = 0; $i <= (count($fieldArys) - 1); $i ++)
        {
          if ($fieldArys[$i] == $keyword)
          {
            $tki = $i;
            continue;
          }
        }
        if (base::getNum($tki, 0) == 0) $tki = 1;
        $tki = $tki * 2 + 1;
        $query = '//xml/' . $base . '/' . $node;
        $rests = $xpath -> query($query);
        foreach ($rests as $rest)
        {
          $nodeValue = $rest -> childNodes -> item($tki) -> nodeValue;
          $karys[$rest -> childNodes -> item(1) -> nodeValue] = $nodeValue;
        }
      }
      return $karys;
    }

    public static function getXMLRoute($argCodeName, $argType)
    {
      $type = $argType;
      $codename = $argCodeName;
      $dir = '';
      $routeStr = base::getLRStr($codename, '.', 'leftr');
      switch($type)
      {
        case 'cfg':
          $dir = 'common';
          break;
        case 'lng':
          $dir = 'common/language';
          break;
        case 'sel':
          $dir = 'common/language';
          break;
        case 'tpl':
          $dir = 'common/template';
          break;
        default:
          $dir = 'common';
          break;
      }
      if (substr($routeStr, 0, 7) == 'global.')
      {
        $routeStr = substr($routeStr, 7, strlen($routeStr) - 7);
        if (is_numeric(strpos($routeStr, ':')))
        {
          $routeStr = base::getLRStr($routeStr, ':', 'left') . '/' . $dir . '/' . base::getLRStr($routeStr, ':', 'right') . XMLSFX;
        }
        else
        {
          $routeStr = $dir . '/' . $routeStr . XMLSFX;
        }
      }
      else
      {
        $genre = page::getPara('genre');
        if (base::isEmpty($routeStr)) $routeStr = base::getLRStr(page::getPara('filename'), '.', 'left');
        $routeStr = $dir . '/' . $routeStr . XMLSFX;
        if (!base::isEmpty($genre)) $routeStr = $genre . '/' . $routeStr;
      }
      $routeStr = smart::getActualRoute($routeStr, 1);
      return $routeStr;
    }

    public static function parse($argString)
    {
      $tmpstr = $argString;
      if (!base::isEmpty($tmpstr))
      {
        $regtag = preg_match_all('/<jtbc[^>]*>(.*?)<\/jtbc>/is', $tmpstr, $regArys);
        if ($regtag)
        {
          for ($i = 0; $i <= count($regArys[0]) - 1; $i ++)
          {
            $tagtext = $regArys[0][$i];
            if (is_numeric(strpos($tagtext, '$function="')) && is_numeric(strpos($tagtext, '$parameter="')))
            {
              $function = base::getLRStr(base::getLRStr($tagtext, '$function="', 'rightr'), '"', 'left');
              $parameter = base::getLRStr(base::getLRStr($tagtext, '$parameter="', 'rightr'), '"', 'left');
              if ($function == 'transfer')
              {
                page::$counter += 1;
                page::$para['jtbctag' . page::$counter] = '{@}' . $regArys[1][$i] . '{@}';
                $evalfunction = $function . '(\'' . $parameter . ';jtbctag=jtbctag' . page::$counter . '\')';
                $tmpstr = str_replace($tagtext, self::getEvalValue($evalfunction), $tmpstr);
              }
            }
          }
        }
        $regm = preg_match_all('({\$=(.[^\}]*)})', $tmpstr, $regArys);
        if ($regm)
        {
          for ($i = 0; $i <= count($regArys[0]) - 1; $i ++)
          {
            $tmpstr = str_replace($regArys[0][$i], self::getEvalValue($regArys[1][$i]), $tmpstr);
          }
        }
      }
      return $tmpstr;
    }

    public static function replaceTagByAry($argString, $argAry, $argMode = 0, $argModeID = 0, $argEncode = 1)
    {
      $string = $argString;
      $ary = $argAry;
      $mode = $argMode;
      $modeid = $argModeID;
      $encode = $argEncode;
      if (!base::isEmpty($string) && is_array($ary))
      {
        foreach ($ary as $key => $val)
        {
          if ($mode >= 10 && $mode < 20)
          {
            $key = base::getLRStr($key, '_', 'rightr');
            if ($mode == 10) $GLOBALS['RS_' . $key] = $val;
            else if ($mode == 11)
            {
              if ($modeid == 0) $GLOBALS['RST_' . $key] = $val;
              else $GLOBALS['RST' . $modeid . '_' . $key] = $val;
            }
          }
          else if ($mode >= 20 && $mode < 30)
          {
            if ($mode == 21)
            {
              if ($modeid == 0) $GLOBALS['RST_' . $key] = $val;
              else $GLOBALS['RST' . $modeid . '_' . $key] = $val;
            }
          }
          if ($encode == 1) $val = base::htmlEncode($val);
          $string = str_replace('{$' . $key . '}', $val, $string);
        }
      }
      return $string;
    }

    public static function replaceOriginalTagByAry($argString, $argAry, $argMode = 0, $argModeID = 0)
    {
      $string = $argString;
      $ary = $argAry;
      $mode = $argMode;
      $modeid = $argModeID;
      $tmpstr = self::replaceTagByAry($string, $ary, $mode, $modeid, 0);
      return $tmpstr;
    }

    public static function setXRootAtt($argSourcefile, $argAtt, $argValue)
    {
      $bool = false;
      $sourceFile = $argSourcefile;
      $att = $argAtt;
      $value = $argValue;
      if (is_file($sourceFile))
      {
        $doc = new DOMDocument();
        $doc -> load($sourceFile);
        $xpath = new DOMXPath($doc);
        $query = '//xml';
        $xpath -> query($query) -> item(0) -> setAttribute($att, $value);
        $bool = $doc -> save($sourceFile);
      }
      return $bool;
    }

    public static function setXMLInfo($argSourcefile, $argKeyword, $argName, $argValue)
    {
      $bool = false;
      $keyword = $argKeyword;
      $sourceFile = $argSourcefile;
      $name = $argName;
      $value = $argValue;
      if (is_file($sourceFile))
      {
        $doc = new DOMDocument();
        $doc -> load($sourceFile);
        $xpath = new DOMXPath($doc);
        $query = '//xml/configure/node';
        $node = $xpath -> query($query) -> item(0) -> nodeValue;
        $query = '//xml/configure/field';
        $field = $xpath -> query($query) -> item(0) -> nodeValue;
        $query = '//xml/configure/base';
        $base = $xpath -> query($query) -> item(0) -> nodeValue;
        $fieldArys = explode(',', $field);
        $tki = 0;
        for ($i = 0; $i <= (count($fieldArys) - 1); $i ++)
        {
          if ($fieldArys[$i] == $keyword)
          {
            $tki = $i;
            continue;
          }
        }
        if (base::getNum($tki, 0) == 0) $tki = 1;
        $tki = $tki * 2 + 1;
        $query = '//xml/' . $base . '/' . $node;
        $rests = $xpath -> query($query);
        foreach ($rests as $rest)
        {
          if ($rest -> childNodes -> item(1) -> nodeValue == $name)
          {
            $rest -> childNodes -> item($tki) -> nodeValue = '';
            $rest -> childNodes -> item($tki) -> appendChild($doc -> createCDATASection($value));
          }
        }
        $bool = $doc -> save($sourceFile);
      }
      return $bool;
    }

    public static function take($argCodeName, $argType = null, $argParse = 0, $argVars = null, $argNodeName = null)
    {
      $result = '';
      $type = $argType;
      $codename = $argCodeName;
      $ns = __NAMESPACE__;
      $parse = base::getNum($argParse, 0);
      $vars = $argVars;
      $nodeName = base::getString($argNodeName);
      if (is_null($type))
      {
        $type = 'tpl';
        $parse = 1;
      }
      $codename = self::getAbbrTransKey($codename);
      $routeStr = self::getXMLRoute($codename, $type);
      $key = base::getLRStr($codename, '.', 'right');
      $activeValue = self::getActiveValue($type);
      if (!base::isEmpty($nodeName)) $activeValue = $nodeName;
      $globalStr = $routeStr;
      $globalStr = str_replace('../', '', $globalStr);
      $globalStr = str_replace(XMLSFX, '', $globalStr);
      $globalStr = str_replace('/', '_', $globalStr);
      $globalStr = APPNAME . $globalStr . '_' . $activeValue;
      if (!is_array(@$GLOBALS[$globalStr])) $GLOBALS[$globalStr] = self::getXMLInfo($routeStr, $activeValue);
      if (isset($GLOBALS[$globalStr][$key]))
      {
        $result = $GLOBALS[$globalStr][$key];
        if ($type == 'tpl')
        {
          $genre = page::getPara('genre');
          $tthis = base::getLRStr($codename, '.', 'leftr');
          $tthisGenre = $genre;
          if (is_numeric(strpos($codename, ':'))) $tthisGenre = base::getLRStr(base::getLRStr($codename, ':', 'leftr'), 'global.', 'right');
          $result = str_replace('{$>genre}', $genre, $result);
          $result = str_replace('{$>this}', $tthis, $result);
          $result = str_replace('{$>this.genre}', $tthisGenre, $result);
          $result = str_replace('{$>now}', base::getDateTime(), $result);
          $result = str_replace('{$>ns}', $ns . '\\', $result);
          if (is_numeric(strpos($genre, '/')))
          {
            $genreAry = explode('/', $genre);
            $genreAryCount = count($genreAry);
            if ($genreAryCount == 2) $result = str_replace('{$>genre.parent}', $genreAry[0], $result);
            else if ($genreAryCount == 3)
            {
              $result = str_replace('{$>genre.parent}', $genreAry[0] . '/' . $genreAry[1], $result);
              $result = str_replace('{$>genre.grandparent}', $genreAry[0], $result);
            }
            else if ($genreAryCount == 4)
            {
              $result = str_replace('{$>genre.parent}', $genreAry[0] . '/' . $genreAry[1] . '/' . $genreAry[2], $result);
              $result = str_replace('{$>genre.grandparent}', $genreAry[0] . '/' . $genreAry[1], $result);
              $result = str_replace('{$>genre.greatgrandparent}', $genreAry[0], $result);
            }
          }
          if (is_numeric(strpos($tthisGenre, '/')))
          {
            $tthisGenreAry = explode('/', $tthisGenre);
            $tthisGenreAryCount = count($tthisGenreAry);
            if ($tthisGenreAryCount == 2) $result = str_replace('{$>this.genre.parent}', $tthisGenreAry[0], $result);
            else if ($tthisGenreAryCount == 3)
            {
              $result = str_replace('{$>this.genre.parent}', $tthisGenreAry[0] . '/' . $tthisGenreAry[1], $result);
              $result = str_replace('{$>this.genre.grandparent}', $tthisGenreAry[0], $result);
            }
            else if ($tthisGenreAryCount == 4)
            {
              $result = str_replace('{$>this.genre.parent}', $tthisGenreAry[0] . '/' . $tthisGenreAry[1] . '/' . $tthisGenreAry[2], $result);
              $result = str_replace('{$>this.genre.grandparent}', $tthisGenreAry[0] . '/' . $tthisGenreAry[1], $result);
              $result = str_replace('{$>this.genre.greatgrandparent}', $tthisGenreAry[0], $result);
            }
          }
        }
        if (is_array($vars))
        {
          foreach ($vars as $key => $val) $result = str_replace('{$' . $key . '}', $val, $result);
        }
        else if (!empty($vars))
        {
          $jsonvars = json_decode($vars, 1);
          if (is_array($jsonvars))
          {
            foreach ($jsonvars as $key => $val) $result = str_replace('{$' . $key . '}', $val, $result);
          }
        }
        if ($parse == 1) $result = self::parse($result);
      }
      if ($key == '*' && base::isEmpty($result)) $result = $GLOBALS[$globalStr];
      return $result;
    }

    public static function takeByNode($argCodeName, $argNodeName = null, $argType = null, $argParse = 0, $argVars = null)
    {
      return self::take($argCodeName, $argType, $argParse, $argVars, $argNodeName);
    }

    public static function takeAndFormat($argCodeName, $argType, $argTpl)
    {
      $tmpstr = '';
      $type = $argType;
      $codename = $argCodeName;
      $tpl = $argTpl;
      $xmlAry = self::take($codename, $type);
      if (is_array($xmlAry))
      {
        $tmpstr = self::take($tpl, 'tpl');
        $tpl = new tpl();
        $tpl -> tplString = $tmpstr;
        $loopString = $tpl -> getLoopString('{@}');
        foreach ($xmlAry as $key => $val)
        {
          $loopLineString = $loopString;
          $loopLineString = str_replace('{$key}', base::htmlEncode($key), $loopLineString);
          $loopLineString = str_replace('{$val}', base::htmlEncode($val), $loopLineString);
          $tpl -> insertLoopLine($loopLineString);
        }
        $tmpstr = $tpl -> mergeTemplate();
        $tmpstr = self::parse($tmpstr);
      }
      return $tmpstr;
    }

    public static function pagi($argNum1, $argNum2, $argBaseLink, $argTplId = '', $argPagiId = 'pagi', $argPagiLen = 5)
    {
      $tmpstr = '';
      $vlNum = 0;
      $num1 = base::getNum($argNum1, 0);
      $num2 = base::getNum($argNum2, 0);
      $pagilen = base::getNum($argPagiLen, 5);
      $baseLink = $argBaseLink;
      $tplId = $argTplId;
      if (base::isEmpty($tplId)) $tplId = 'pagi-1';
      $pagiId = $argPagiId;
      if (is_numeric(strpos($pagiId, 'pagi-ct'))) $vlNum = 1;
      if ($num2 > $vlNum)
      {
        $tmpstr = self::take('global.config.' . $tplId, 'tpl');
        $tpl = new tpl();
        $tpl -> tplString = $tmpstr;
        $loopString = $tpl -> getLoopString('{@}');
        if ($num1 < 1) $num1 = 1;
        if ($num1 > $num2) $num1 = $num2;
        $num1c = floor($num1 - floor($pagilen / 2));
        if ($num1c < 1) $num1c = 1;
        $num1s = $num1c + $pagilen - 1;
        if ($num1s > $num2) $num1s = $num2;
        if ($num1c <= $num1s)
        {
          if (($num1s - $num1c) < ($pagilen - 1))
          {
            $num1c = $num1c - (($pagilen - 1) - ($num1s - $num1c));
            if ($num1c < 1) $num1c = 1;
          }
          for ($ti = $num1c; $ti <= $num1s; $ti ++)
          {
            $loopLineString = $loopString;
            $loopLineString = str_replace('{$-num}', $ti, $loopLineString);
            $loopLineString = str_replace('{$-link}', base::htmlEncode(str_replace('[~page]', $ti, $baseLink)), $loopLineString);
            if ($ti != $num1) $loopLineString = str_replace('{$-class}', '', $loopLineString);
            else $loopLineString = str_replace('{$-class}', 'on', $loopLineString);
            $tpl -> insertLoopLine($loopLineString);
          }
        }
        $tmpstr = $tpl -> mergeTemplate();
        $tmpstr = str_replace('{$-page1}', $num1, $tmpstr);
        $tmpstr = str_replace('{$-page2}', $num2, $tmpstr);
        $tmpstr = str_replace('{$-firstpagelink}', base::htmlEncode(str_replace('[~page]', '1', $baseLink)), $tmpstr);
        $tmpstr = str_replace('{$-lastpagelink}', base::htmlEncode(str_replace('[~page]', $num2, $baseLink)), $tmpstr);
        $tmpstr = str_replace('{$-pagiid}', base::htmlEncode($pagiId), $tmpstr);
        $tmpstr = str_replace('{$-baselink}', base::htmlEncode($baseLink), $tmpstr);
        $tmpstr = str_replace('{$-next-page-num}', ($num1 == $num1s? $num1: ($num1 + 1)), $tmpstr);
        $tmpstr = self::parse($tmpstr);
      }
      return $tmpstr;
    }

    public static function xmlSelect($argString, $argValue, $argTemplate, $argName = '')
    {
      $tmpstr = '';
      $string = $argString;
      $value = $argValue;
      $template = $argTemplate;
      $name = $argName;
      $xinfostr = $string;
      $selstr = '';
      if (is_numeric(strpos($string, '|')))
      {
        $xinfostr = base::getLRStr($string, '|', 'left');
        $selstr = base::getLRStr($string, '|', 'right');
      }
      $xmlAry = self::take($xinfostr, 'sel');
      if (is_array($xmlAry))
      {
        $optionUnselected = self::take('global.config.xmlselect_un' . $template, 'tpl');
        $optionselected = self::take('global.config.xmlselect_' . $template, 'tpl');
        foreach ($xmlAry as $key => $val)
        {
          if (base::isEmpty($selstr) || base::checkInstr($selstr, $key, ','))
          {
            if ($value == '*' || base::checkInstr($value, $key, ',')) $tmpstr .= $optionselected;
            else $tmpstr .= $optionUnselected;
            $tmpstr = str_replace('{$explain}', base::htmlEncode($val), $tmpstr);
            $tmpstr = str_replace('{$value}', base::htmlEncode($key), $tmpstr);
          }
        }
        $tmpstr = str_replace('{$name}', base::htmlEncode($name), $tmpstr);
        $tmpstr = self::parse($tmpstr);
      }
      return $tmpstr;
    }

  }
}
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
?>
