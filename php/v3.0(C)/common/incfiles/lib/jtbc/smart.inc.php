<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace jtbc {
  class smart
  {
    public static function createURL($argType, $argKey, $argVars = null, $argGenre = null)
    {
      $tmpstr = '';
      $type = $argType;
      $key = $argKey;
      $vars = $argVars;
      $genre = $argGenre;
      if (is_null($genre)) $genre = page::getPara('genre');
      $urltype = base::getNum(tpl::take('global.' . $genre . ':config.urltype', 'cfg'), 0);
      switch($urltype)
      {
        case 0:
          switch($type)
          {
            case 'list':
              $tmpstr = '?type=list&category=' . base::getNum($key, 0);
              if (is_array($vars))
              {
                if (array_key_exists('page', $vars)) $tmpstr .= '&page=' . base::getString($vars['page']);
              }
              break;
            case 'detail':
              $tmpstr = '?type=detail&id=' . base::getNum($key, 0);
              if (is_array($vars))
              {
                if (array_key_exists('page', $vars)) $tmpstr .= '&page=' . base::getString($vars['page']);
              }
              break;
          }
          break;
        case 1:
          switch($type)
          {
            case 'list':
              $tmpstr = 'list-' . base::getNum($key, 0);
              if (is_array($vars))
              {
                if (array_key_exists('page', $vars)) $tmpstr .= '-' . base::getString($vars['page']);
              }
              $tmpstr .= '.html';
              break;
            case 'detail':
              $tmpstr = 'detail-' . base::getNum($key, 0);
              if (is_array($vars))
              {
                if (array_key_exists('page', $vars)) $tmpstr .= '-' . base::getString($vars['page']);
              }
              $tmpstr .= '.html';
              break;
          }
          break;
      }
      return $tmpstr;
    }

    public static function dbFieldSwitch($argTable, $argPrefix, $argField, $argIds, $argValue = null)
    {
      $exec = 0;
      $table = $argTable;
      $prefix = $argPrefix;
      $field = $argField;
      $ids = $argIds;
      $value = $argValue;
      $db = page::db();
      if (!is_null($db) && base::cIdAry($ids))
      {
        $sqlstr = "update " . $table . " set " . $prefix . $field . "=abs(" . $prefix . $field . "-1) where " . $prefix . "id in (" . $ids . ")";
        if (is_numeric($value)) $sqlstr = "update " . $table . " set " . $prefix . $field . "=" . base::getNum($value, 0) . " where " . $prefix . "id in (" . $ids . ")";
        $exec = $db -> exec($sqlstr);
      }
      return $exec;
    }

    public static function dbFieldNumberAdd($argTable, $argPrefix, $argField, $argIds, $argValue = 1)
    {
      $exec = 0;
      $table = $argTable;
      $prefix = $argPrefix;
      $field = $argField;
      $ids = $argIds;
      $value = base::getNum($argValue, 0);
      $db = page::db();
      if (!is_null($db) && base::cIdAry($ids))
      {
        $sqlstr = "update " . $table . " set " . $prefix . $field . "=" . $prefix . $field . "+" . $value . " where " . $prefix . "id in (" . $ids . ")";
        $exec = $db -> exec($sqlstr);
      }
      return $exec;
    }

    public static function getRoute()
    {
      $route = '';
      if (is_file('common/root.jtbc')) $route = 'root';
      else if (is_file('../common/root.jtbc')) $route = 'node';
      else if (is_file('../../common/root.jtbc')) $route = 'child';
      else if (is_file('../../../common/root.jtbc')) $route = 'grandson';
      else if (is_file('../../../../common/root.jtbc')) $route = 'greatgrandson';
      return $route;
    }

    public static function getForeLang()
    {
      $language = LANGUAGE;
      $lang = base::getNum(tpl::take('global.config.lang-' . $language, 'cfg'), 0);
      $cookieValue = base::getString(@$_COOKIE[APPNAME . 'config']['language']);
      if (!base::isEmpty($cookieValue))
      {
        $cookieLang = base::getNum(tpl::take('global.config.lang-' . $cookieValue, 'cfg'), -1);
        if ($cookieLang != -1) $lang = $cookieLang;
      }
      return $lang;
    }

    public static function getActualRoute($argRoutestr, $argType = 0)
    {
      $route = '';
      $type = $argType;
      $routeStr = $argRoutestr;
      if ($type == 0 && !base::isEmpty(BASEDIR)) $route = BASEDIR . $routeStr;
      else
      {
        switch (page::getPara('route'))
        {
          case 'greatgrandson':
            $route = '../../../../' . $routeStr;
            break;
          case 'grandson':
            $route = '../../../' . $routeStr;
            break;
          case 'child':
            $route = '../../' . $routeStr;
            break;
          case 'node':
            $route = '../' . $routeStr;
            break;
          default:
            $route = $routeStr;
            break;
        }
      }
      return $route;
    }

    public static function getActualGenre($argRoute)
    {
      $tgenre = '';
      $route = $argRoute;
      $routeStr = $_SERVER['SCRIPT_NAME'];
      $routeStr = base::getLRStr($routeStr, '/', 'leftr');
      $ary = explode('/', $routeStr);
      $arycount = count($ary);
      switch ($route)
      {
        case 'greatgrandson':
          if ($arycount >= 4) $tgenre = $ary[$arycount - 4] . '/' . $ary[$arycount - 3] . '/' . $ary[$arycount - 2] . '/' . $ary[$arycount - 1];
          break;
        case 'grandson':
          if ($arycount >= 3) $tgenre = $ary[$arycount - 3] . '/' . $ary[$arycount - 2] . '/' . $ary[$arycount - 1];
          break;
        case 'child':
          if ($arycount >= 2) $tgenre = $ary[$arycount - 2] . '/' . $ary[$arycount - 1];
          break;
        case 'node':
          if ($arycount >= 1) $tgenre = $ary[$arycount - 1];
          break;
        default:
          $tgenre = '';
          break;
      }
      return $tgenre;
    }

    public static function getFolderByGuide($argFilePrefix = 'guide', $argPath = '')
    {
      $list = '';
      $order = '';
      $path = $argPath;
      $fileprefix = $argFilePrefix;
      if (base::isEmpty($path)) $path = self::getActualRoute('./', 1);
      $webdir = dir($path);
      $myguide = $path . '/common/guide' . XMLSFX;
      if (file_exists($myguide)) $order = tpl::getXRootAtt($myguide, 'order');
      while($entry = $webdir -> read())
      {
        if (!(is_numeric(strpos($entry, '.'))))
        {
          if (!(base::cInstr($order, $entry, ',')))
          {
            $order .= ',' . $entry;
          }
        }
      }
      $webdir -> close();
      $orderary = explode(',', $order);
      if (is_array($orderary))
      {
        foreach($orderary as $key => $val)
        {
          if (!base::isEmpty($val))
          {
            $filename = $path . $val . '/common/' . $fileprefix . XMLSFX;
            if (file_exists($filename))
            {
              $list .= $path . $val . '|+|';
              if (tpl::getXRootAtt($filename, 'mode') == 'jtbcf') $list .= self::getFolderByGuide($fileprefix, $path . $val . '/');
            }
          }
        }
      }
      return $list;
    }

    public static function getCutKeywordSQL($argField, $argKeyword)
    {
      $sql = '';
      $field = $argField;
      $keyword = $argKeyword;
      if (!base::isEmpty($keyword))
      {
        $keywordAry = explode(' ', $keyword);
        foreach ($keywordAry as $key => $val)
        {
          if (!base::isEmpty($val)) $sql .= " and " . $field . " like '%" . addslashes($val) . "%'";
        }
      }
      return $sql;
    }

    public static function getAutoInsertSQLByVars($argTable, $argVars)
    {
      $table = $argTable;
      $vars = $argVars;
      $tmpstr = self::getAutoRequestInsertSQL($table, null, $vars, null, '', '', 1);
      return $tmpstr;
    }

    public static function getAutoRequestInsertSQL($argTable, $argSpecialFiled, $argVars = null, $argSource = null, $argNamePre = '', $argNameSuffix = '', $argMode = 0)
    {
      $tmpstr = '';
      $table = $argTable;
      $specialFiled = $argSpecialFiled;
      $namePre = $argNamePre;
      $nameSuffix = $argNameSuffix;
      $vars = $argVars;
      $source = $argSource;
      $mode = base::getNum($argMode, 0);
      $db = page::db();
      if (!is_null($db))
      {
        $desc = $db -> desc($table);
        if (is_array($desc))
        {
          $fieldString = '';
          $fieldValues = '';
          $tmpstr = 'insert into ' . $table . ' (';
          foreach ($desc as $i => $item)
          {
            $filedValid = false;
            $filedName = $item['Field'];
            $filedType = $item['Type'];
            $filedTypeN = $filedType;
            $filedTypeL = null;
            if (is_numeric(strpos($filedType, '(')))
            {
              $filedTypeN = base::getLRStr($filedType, '(', 'left');
              $filedTypeL = base::getNum(base::getLRStr(base::getLRStr($filedType, '(', 'right'), ')', 'left'), 0);
            }
            $requestValue = '';
            $requestName = base::getLRStr($filedName, '_', 'rightr');
            if (!base::isEmpty($namePre)) $requestName = $namePre . $requestName;
            if (!base::isEmpty($nameSuffix)) $requestName = $requestName . $nameSuffix;
            if (is_array($vars)) $requestValue = base::getString(@$vars[$filedName]);
            if ($mode == 0)
            {
              if (!base::cInstr($specialFiled, $filedName, ','))
              {
                $filedValid = true;
                if (base::isEmpty($requestValue))
                {
                  if (is_array($source)) $requestValue = base::getString($source[$requestName]);
                  else $requestValue = base::getString(page::getHTTPPara($requestName, 'post'));
                }
              }
            }
            else if ($mode == 1)
            {
              if (is_array($vars))
              {
                if (array_key_exists($filedName, $vars)) $filedValid = true;
              }
            }
            if ($filedValid == true)
            {
              if ($filedTypeN == 'int' || $filedTypeN == 'integer')
              {
                $fieldString .= $filedName . ',';
                $fieldValues .= base::getNum($requestValue, 0) . ',';
              }
              else if ($filedTypeN == 'varchar')
              {
                $fieldString .= $filedName . ',';
                $fieldValues .= '\'' . addslashes(base::getLeft($requestValue, $filedTypeL)) . '\',';
              }
              else if ($filedTypeN == 'datetime')
              {
                $fieldString .= $filedName . ',';
                $fieldValues .= '\'' . addslashes(base::getDateTime($requestValue)) . '\',';
              }
              else if ($filedTypeN == 'text')
              {
                $fieldString .= $filedName . ',';
                $fieldValues .= '\'' . addslashes(base::getLeft($requestValue, 20000)) . '\',';
              }
              else if ($filedTypeN == 'mediumtext')
              {
                $fieldString .= $filedName . ',';
                $fieldValues .= '\'' . addslashes(base::getLeft($requestValue, 5000000)) . '\',';
              }
              else if ($filedTypeN == 'longtext')
              {
                $fieldString .= $filedName . ',';
                $fieldValues .= '\'' . addslashes(base::getLeft($requestValue, 1000000000)) . '\',';
              }
            }
          }
          if (!base::isEmpty($fieldString)) $fieldString = base::getLRStr($fieldString, ',', 'leftr');
          if (!base::isEmpty($fieldValues)) $fieldValues = base::getLRStr($fieldValues, ',', 'leftr');
          $tmpstr .= $fieldString;
          $tmpstr .= ') values (';
          $tmpstr .= $fieldValues;
          $tmpstr .= ')';
        }
      }
      return $tmpstr;
    }

    public static function getAutoUpdateSQLByVars($argTable, $argIdFiled, $argId, $argVars)
    {
      $table = $argTable;
      $vars = $argVars;
      $idFiled = $argIdFiled;
      $id = base::getNum($argId, 0);
      $tmpstr = self::getAutoRequestUpdateSQL($table, null, $idFiled, $id, $vars, null, '', '', 1);
      return $tmpstr;
    }

    public static function getAutoRequestUpdateSQL($argTable, $argSpecialFiled, $argIdFiled, $argId, $argVars = null, $argSource = null, $argNamePre = '', $argNameSuffix = '', $argMode = 0)
    {
      $tmpstr = '';
      $table = $argTable;
      $specialFiled = $argSpecialFiled;
      $idFiled = $argIdFiled;
      $id = base::getNum($argId, 0);
      $namePre = $argNamePre;
      $nameSuffix = $argNameSuffix;
      $vars = $argVars;
      $source = $argSource;
      $mode = base::getNum($argMode, 0);
      $db = page::db();
      if (!is_null($db))
      {
        $desc = $db -> desc($table);
        if (is_array($desc))
        {
          $fieldStringValues = '';
          $tmpstr = 'update ' . $table . ' set ';
          foreach ($desc as $i => $item)
          {
            $filedValid = false;
            $filedName = $item['Field'];
            $filedType = $item['Type'];
            $filedTypeN = $filedType;
            $filedTypeL = null;
            if (is_numeric(strpos($filedType, '(')))
            {
              $filedTypeN = base::getLRStr($filedType, '(', 'left');
              $filedTypeL = base::getNum(base::getLRStr(base::getLRStr($filedType, '(', 'right'), ')', 'left'), 0);
            }
            $requestValue = '';
            $requestName = base::getLRStr($filedName, '_', 'rightr');
            if (!base::isEmpty($namePre)) $requestName = $namePre . $requestName;
            if (!base::isEmpty($nameSuffix)) $requestName = $requestName . $nameSuffix;
            if (is_array($vars)) $requestValue = base::getString(@$vars[$filedName]);

            if ($mode == 0)
            {
              if (!base::cInstr($specialFiled, $filedName, ','))
              {
                $filedValid = true;
                if (base::isEmpty($requestValue))
                {
                  if (is_array($source)) $requestValue = base::getString($source[$requestName]);
                  else $requestValue = base::getString(page::getHTTPPara($requestName, 'post'));
                }
              }
            }
            else if ($mode == 1)
            {
              if (is_array($vars))
              {
                if (array_key_exists($filedName, $vars)) $filedValid = true;
              }
            }
            if ($filedValid == true)
            {
              if ($filedTypeN == 'int' || $filedTypeN == 'integer')
              {
                $fieldStringValues .= $filedName . '=' . base::getNum($requestValue, 0) . ',';
              }
              else if ($filedTypeN == 'varchar')
              {
                $fieldStringValues .= $filedName . '=\'' . addslashes(base::getLeft($requestValue, $filedTypeL)) . '\',';
              }
              else if ($filedTypeN == 'datetime')
              {
                $fieldStringValues .= $filedName . '=\'' . addslashes(base::getDateTime($requestValue)) . '\',';
              }
              else if ($filedTypeN == 'text')
              {
                $fieldStringValues .= $filedName . '=\'' . addslashes(base::getLeft($requestValue, 20000)) . '\',';
              }
              else if ($filedTypeN == 'mediumtext')
              {
                $fieldStringValues .= $filedName . '=\'' . addslashes(base::getLeft($requestValue, 5000000)) . '\',';
              }
              else if ($filedTypeN == 'longtext')
              {
                $fieldStringValues .= $filedName . '=\'' . addslashes(base::getLeft($requestValue, 1000000000)) . '\',';
              }
            }
          }
          if (!base::isEmpty($fieldStringValues)) $fieldStringValues = base::getLRStr($fieldStringValues, ',', 'leftr');
          $tmpstr .= $fieldStringValues;
          $tmpstr .= ' where ' . $idFiled . '=' . $id;
        }
      }
      return $tmpstr;
    }

    public static function replaceKeyWordHighlight($argString, $argKeyword = null)
    {
      $string = $argString;
      $keyword = $argKeyword;
      $spkey = 'jtbc~$~key~$~jtbc';
      if (!base::isEmpty($string))
      {
        if (!is_null($keyword))
        {
          $keywordAry = explode(' ', $keyword);
          $string = str_replace('*', $spkey, $string);
          foreach ($keywordAry as $key => $val)
          {
            if (!base::isEmpty($val))
            {
              $string = str_replace($val, '*key*' . $val . '*yek*', $string);
            }
          }
        }
        else
        {
          $string = str_replace('*key*', '<span class="highlight">', $string);
          $string = str_replace('*yek*', '</span>', $string);
          $string = str_replace($spkey, '*', $string);
        }
      }
      return $string;
    }

    public static function transfer($argPara, $argOSQLAry = null)
    {
      $tmpstr = '';
      $db = page::db();
      $genre = page::getPara('genre');
      $lang = self::getForeLang();
      $para = $argPara;
      $osqlAry = $argOSQLAry;
      $paraTpl = base::getParameter($para, 'tpl');
      $paraJTBCTag = base::getParameter($para, 'jtbctag');
      $paraType = base::getParameter($para, 'type');
      $paraGenre = base::getParameter($para, 'genre');
      $paraDBTable = base::getParameter($para, 'db_table');
      $paraDBPrefix = base::getParameter($para, 'db_prefix');
      $paraOSQL = base::getParameter($para, 'osql');
      $paraOSQLOrder = base::getParameter($para, 'osqlorder');
      $paraRowFilter = base::getParameter($para, 'rowfilter');
      $paraBaseURL = base::getParameter($para, 'baseurl');
      $paraCache = base::getParameter($para, 'cache');
      $paraCacheTimeout = base::getNum(base::getParameter($para, 'cachetimeout'), 300);
      $paraVars = base::getParameter($para, 'vars');
      $paraLimit = base::getNum(base::getParameter($para, 'limit'), 0);
      $paraCategory = base::getNum(base::getParameter($para, 'category'), 0);
      $paraLang = base::getNum(base::getParameter($para, 'lang'), -100);
      $paraTransferID = base::getNum(base::getParameter($para, 'transferid'), 0);
      if ($paraLimit == 0) $paraLimit = 10;
      if ($paraLang == -100) $paraLang = $lang;
      $ns = __NAMESPACE__;
      $cacheAry = null;
      if (!base::isEmpty($paraCache))
      {
        $cacheData = cache::get($paraCache);
        if (is_array($cacheData))
        {
          if (count($cacheData) == 2)
          {
            $cacheAry = $cacheData[1];
            $cacheTimeStamp = $cacheData[0];
            if ((time() - $cacheTimeStamp) >= $paraCacheTimeout) cache::remove($paraCache);
          }
        }
      }
      if (base::isEmpty($paraBaseURL))
      {
        if (!base::isEmpty($paraGenre) && $paraGenre != $genre)
        {
          $paraBaseURL = smart::getActualRoute($paraGenre);
          if (base::getRight($paraBaseURL, 1) != '/') $paraBaseURL .= '/';
        }
      }
      if (base::isEmpty($paraGenre)) $paraGenre = $genre;
      if (base::isEmpty($paraDBTable)) $paraDBTable = tpl::take('global.' . $paraGenre . ':config.db_table', 'cfg');
      if (base::isEmpty($paraDBPrefix)) $paraDBPrefix = tpl::take('global.' . $paraGenre . ':config.db_prefix', 'cfg');
      if (!base::isEmpty($paraDBTable))
      {
        $sqlstr = '';
        $sqlorderstr = '';
        switch($paraType)
        {
          case 'count':
            $sqlstr = "select count(*) as rscount from " . $paraDBTable . " where " . $paraDBPrefix . "delete=0 and " . $paraDBPrefix . "publish=1";
            $sqlorderstr = " order by " . $paraDBPrefix . "id desc";
            break;
          case '@count':
            $sqlstr = "select count(*) as rscount from " . $paraDBTable . " where " . $paraDBPrefix . "delete=0";
            $sqlorderstr = " order by " . $paraDBPrefix . "id desc";
            break;
          case 'new':
            $sqlstr = "select * from " . $paraDBTable . " where " . $paraDBPrefix . "delete=0 and " . $paraDBPrefix . "publish=1";
            $sqlorderstr = " order by " . $paraDBPrefix . "time desc";
            break;
          case '@new':
            $sqlstr = "select * from " . $paraDBTable . " where " . $paraDBPrefix . "delete=0";
            $sqlorderstr = " order by " . $paraDBPrefix . "time desc";
            break;
          case 'top':
            $sqlstr = "select * from " . $paraDBTable . " where " . $paraDBPrefix . "delete=0 and " . $paraDBPrefix . "publish=1";
            $sqlorderstr = " order by " . $paraDBPrefix . "id desc";
            break;
          case '@top':
            $sqlstr = "select * from " . $paraDBTable . " where " . $paraDBPrefix . "delete=0";
            $sqlorderstr = " order by " . $paraDBPrefix . "id desc";
            break;
          case 'commendatory':
            $sqlstr = "select * from " . $paraDBTable . " where " . $paraDBPrefix . "delete=0 and " . $paraDBPrefix . "publish=1 and " . $paraDBPrefix . "commendatory=1";
            $sqlorderstr = " order by " . $paraDBPrefix . "time desc";
            break;
          case '@commendatory':
            $sqlstr = "select * from " . $paraDBTable . " where " . $paraDBPrefix . "delete=0 and " . $paraDBPrefix . "commendatory=1";
            $sqlorderstr = " order by " . $paraDBPrefix . "time desc";
            break;
          case 'order':
            $sqlstr = "select * from " . $paraDBTable . " where " . $paraDBPrefix . "delete=0 and " . $paraDBPrefix . "publish=1";
            $sqlorderstr = " order by " . $paraDBPrefix . "order asc";
            break;
          case '@order':
            $sqlstr = "select * from " . $paraDBTable . " where " . $paraDBPrefix . "delete=0";
            $sqlorderstr = " order by " . $paraDBPrefix . "order asc";
            break;
          default:
            $sqlstr = "select * from " . $paraDBTable . " where " . $paraDBPrefix . "delete=0";
            $sqlorderstr = " order by " . $paraDBPrefix . "id desc";
            break;
        }
        if ($paraLang != -1) $sqlstr .= " and " . $paraDBPrefix . "lang=" . $paraLang;
        if ($paraCategory != 0)
        {
          if (method_exists($ns . '\\universal\\category', 'getCategoryChildID'))
          {
            $sqlstr .= " and " . $paraDBPrefix . "category in (" . base::mergeIdAry($paraCategory, universal\category::getCategoryChildID($paraGenre, $paraLang, $paraCategory)) . ")";
          }
        }
        if (!base::isEmpty($paraOSQL)) $sqlstr .= $paraOSQL;
        if (!base::isEmpty($paraOSQLOrder)) $sqlorderstr = $paraOSQLOrder;
        if (is_array($osqlAry))
        {
          foreach ($osqlAry as $key => $val)
          {
            $valType = gettype($val);
            if ($valType == 'integer') $sqlstr .= " and " . $paraDBPrefix . $key . "=" . base::getNum($val, 0);
            else if ($valType == 'string') $sqlstr .= " and " . $paraDBPrefix . $key . "='" . addslashes($val) . "'";
          }
        }
        $sqlstr .= $sqlorderstr;
        $sqlstr .= ' limit 0,' . $paraLimit;
        if (!base::isEmpty($paraTpl))
        {
          if (strpos($paraTpl, '.')) $tmpstr = tpl::take($paraTpl, 'tpl');
          else $tmpstr = tpl::take('global.transfer.' . $paraTpl, 'tpl');
        }
        else if (!base::isEmpty($paraJTBCTag)) $tmpstr = page::getPara($paraJTBCTag);
        if (!base::isEmpty($paraVars))
        {
          $paraVarsAry = explode('|', $paraVars);
          foreach ($paraVarsAry as $key => $val)
          {
            if (!base::isEmpty($val))
            {
              $valAry = explode('=', $val);
              if (count($valAry) == 2) $tmpstr = str_replace('{$' . $valAry[0] + '}', $valAry[1], $tmpstr);
            }
          }
        }
        $myAry = $cacheAry;
        if (!is_array($myAry))
        {
          if (!is_null($db))
          {
            $myAry = array();
            $rq = $db -> query($sqlstr);
            while($rs = $rq -> fetch()) array_push($myAry, $rs);
            if (!base::isEmpty($paraCache))
            {
              $cacheData = array();
              $cacheData[0] = time();
              $cacheData[1] = $myAry;
              @cache::put($paraCache, $cacheData);
            }
          }
        }
        if (is_array($myAry) && !empty($myAry))
        {
          $rsindex = 1;
          $tpl = new tpl();
          $tpl -> tplString = $tmpstr;
          $loopString = $tpl -> getLoopString('{@}');
          foreach ($myAry as $myKey => $myVal)
          {
            if (base::isEmpty($paraRowFilter) || !base::cInstr($paraRowFilter, $rsindex))
            {
              $loopLineString = $loopString;
              foreach ($myVal as $key => $val)
              {
                $key = base::getLRStr($key, '_', 'rightr');
                if ($paraTransferID == 0) $GLOBALS['RST_' . $key] = $val;
                else $GLOBALS['RST' . $paraTransferID . '_' . $key] = $val;
                $loopLineString = str_replace('{$' . $key . '}', base::htmlEncode($val), $loopLineString);
              }
              $loopLineString = str_replace('{$-i}', base::htmlEncode($rsindex), $loopLineString);
              $loopLineString = str_replace('{$-genre}', base::htmlEncode($paraGenre), $loopLineString);
              $loopLineString = str_replace('{$-lang}', base::htmlEncode($paraLang), $loopLineString);
              $loopLineString = str_replace('{$-baseurl}', base::htmlEncode($paraBaseURL), $loopLineString);
              $loopLineString = tpl::parse($loopLineString);
              $tpl -> insertLoopLine($loopLineString);
            }
            $rsindex += 1;
          }
          $tmpstr = $tpl -> mergeTemplate();
          $tmpstr = str_replace('{$-genre}', base::htmlEncode($paraGenre), $tmpstr);
          $tmpstr = str_replace('{$-lang}', base::htmlEncode($paraLang), $tmpstr);
          $tmpstr = str_replace('{$-baseurl}', base::htmlEncode($paraBaseURL), $tmpstr);
          $tmpstr = tpl::parse($tmpstr);
        }
        else $tmpstr = '';
      }
      return $tmpstr;
    }
  }
}
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
?>