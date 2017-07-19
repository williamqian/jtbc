<?php
namespace jtbc;
class ui extends page {
  public static $account = null;

  public static function account()
  {
    $account = null;
    if (!is_null(self::$account)) $account = self::$account;
    else $account = self::$account = new console\account();
    return $account;
  }

  public static function ppGetFileJSON($argRs, $argPrefix = '')
  {
    $tmpstr = '';
    $rs = $argRs;
    $prefix = $argPrefix;
    if (is_array($rs))
    {
      $paraArray = array();
      $paraArray['filename'] = $rs[$prefix . 'topic'];
      $paraArray['filesize'] = $rs[$prefix . 'filesize'];
      $paraArray['filetype'] = $rs[$prefix . 'filetype'];
      $paraArray['filepath'] = $rs[$prefix . 'filepath'];
      $paraArray['fileurl'] = $rs[$prefix . 'fileurl'];
      $paraArray['filesizetext'] = base::formatFileSize(base::getNum($paraArray['filesize'], 0));
      $tmpstr = json_encode($paraArray);
    }
    return $tmpstr;
  }

  public static function moduleList()
  {
    $status = 1;
    $tmpstr = '';
    $selectmode = 'single';
    $mode = base::getString(request::getHTTPPara('mode', 'get'));
    $keyword = base::getString(request::getHTTPPara('keyword', 'get'));
    $sort = base::getNum(request::getHTTPPara('sort', 'get'), 1);
    $filegroup = base::getNum(request::getHTTPPara('filegroup', 'get'), -1);
    if ($mode == 'multiple') $selectmode = 'multiple';
    $db = self::db();
    if (!is_null($db))
    {
      $account = self::account();
      $tmpstr = tpl::take('managerapi.list', 'tpl');
      $tpl = new tpl();
      $tpl -> tplString = $tmpstr;
      $loopString = $tpl -> getLoopString('{@}');
      $table = tpl::take('config.db_table', 'cfg');
      $prefix = tpl::take('config.db_prefix', 'cfg');
      $sqlstr = "select * from " . $table . " where " . $prefix . "delete=0 and " . $prefix . "lang=" . $account -> getLang();
      if ($filegroup != -1) $sqlstr .= " and " . $prefix . "filegroup=" . $filegroup;
      if (!base::isEmpty($keyword)) $sqlstr .= smart::getCutKeywordSQL($prefix . 'topic', $keyword);
      if ($sort == 1) $sqlstr .= " order by " . $prefix . "hot desc," . $prefix . "id desc";
      else $sqlstr .= " order by " . $prefix . "time desc," . $prefix . "id desc";
      $sqlstr .= " limit 100";
      $rq = $db -> query($sqlstr);
      while($rs = $rq -> fetch())
      {
        $loopLineString = $loopString;
        foreach ($rs as $key => $val)
        {
          $key = base::getLRStr($key, '_', 'rightr');
          $GLOBALS['RS_' . $key] = $val;
          $loopLineString = str_replace('{$' . $key . '}', base::htmlEncode($val), $loopLineString);
          if (!is_numeric($key) && $key == 'topic') $loopLineString = str_replace('{$-topic-keyword-highlight}', smart::replaceKeyWordHighlight(base::htmlEncode(smart::replaceKeyWordHighlight($val, $keyword))), $loopLineString);
        }
        $loopLineString = str_replace('{$-filejson}', base::htmlEncode(self::ppGetFileJSON($rs, $prefix)), $loopLineString);
        $tpl -> insertLoopLine($loopLineString);
      }
      $tmpstr = $tpl -> mergeTemplate();
      $tmpstr = str_replace('{$-selectmode}', base::htmlEncode($selectmode), $tmpstr);
      $tmpstr = str_replace('{$-filegroup}', base::htmlEncode($filegroup), $tmpstr);
      $tmpstr = str_replace('{$-sort}', base::htmlEncode($sort), $tmpstr);
      $tmpstr = str_replace('{$-keyword}', base::htmlEncode($keyword), $tmpstr);
      $tmpstr = tpl::parse($tmpstr);
    }
    $tmpstr = self::formatResult($status, $tmpstr);
    return $tmpstr;
  }

  public static function moduleActionHot()
  {
    $tmpstr = '';
    $status = 0;
    $message = '';
    $id = base::getNum(request::getHTTPPara('id', 'get'), 0);
    $table = tpl::take('config.db_table', 'cfg');
    $prefix = tpl::take('config.db_prefix', 'cfg');
    if (smart::dbFieldNumberAdd($table, $prefix, 'hot', $id)) $status = 1;
    $tmpstr = self::formatMsgResult($status, $message);
    return $tmpstr;
  }

  public static function getResult()
  {
    $tmpstr = '';
    $account = self::account();
    if ($account -> checkLogin())
    {
      $tmpstr = parent::getResult();
    }
    return $tmpstr;
  }
}
?>
