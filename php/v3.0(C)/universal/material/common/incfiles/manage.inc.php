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

  public static function moduleEdit()
  {
    $status = 1;
    $tmpstr = '';
    $id = base::getNum(request::getHTTPPara('id', 'get'), 0);
    $account = self::account();
    if ($account -> checkPopedom(self::getPara('genre'), 'edit'))
    {
      $db = self::db();
      if (!is_null($db))
      {
        $table = tpl::take('config.db_table', 'cfg');
        $prefix = tpl::take('config.db_prefix', 'cfg');
        $sqlstr = "select * from " . $table . " where " . $prefix . "delete=0 and " . $prefix . "id=" . $id;
        $rq = $db -> query($sqlstr);
        $rs = $rq -> fetch();
        if (is_array($rs))
        {
          $tmpstr = tpl::take('manage.edit', 'tpl');
          foreach ($rs as $key => $val)
          {
            $key = base::getLRStr($key, '_', 'rightr');
            $GLOBALS['RS_' . $key] = $val;
            $tmpstr = str_replace('{$' . $key . '}', base::htmlEncode($val), $tmpstr);
          }
          $tmpstr = tpl::parse($tmpstr);
          $tmpstr = $account -> replaceAccountTag($tmpstr);
        }
      }
    }
    $tmpstr = self::formatResult($status, $tmpstr);
    return $tmpstr;
  }

  public static function moduleList()
  {
    $status = 1;
    $tmpstr = '';
    $page = base::getNum(request::getHTTPPara('page', 'get'), 0);
    $filegroup = base::getNum(request::getHTTPPara('filegroup', 'get'), -1);
    $pagesize = base::getNum(tpl::take('config.pagesize', 'cfg'), 0);
    $db = self::db();
    if (!is_null($db))
    {
      $account = self::account();
      $tmpstr = tpl::take('manage.list', 'tpl');
      $tpl = new tpl();
      $tpl -> tplString = $tmpstr;
      $loopString = $tpl -> getLoopString('{@}');
      $table = tpl::take('config.db_table', 'cfg');
      $prefix = tpl::take('config.db_prefix', 'cfg');
      $sqlstr = "select * from " . $table . " where " . $prefix . "delete=0 and " . $prefix . "lang=" . $account -> getLang();
      if ($filegroup != -1) $sqlstr .= " and " . $prefix . "filegroup=" . $filegroup;
      $sqlstr .= " order by " . $prefix . "time desc," . $prefix . "id desc";
      $pagi = new pagi($db);
      $rsAry = $pagi -> getDataAry($sqlstr, $page, $pagesize);
      if (is_array($rsAry))
      {
        foreach($rsAry as $rs)
        {
          $loopLineString = $loopString;
          foreach ($rs as $key => $val)
          {
            $key = base::getLRStr($key, '_', 'rightr');
            $GLOBALS['RS_' . $key] = $val;
            $loopLineString = str_replace('{$' . $key . '}', base::htmlEncode($val), $loopLineString);
          }
          $tpl -> insertLoopLine($loopLineString);
        }
      }
      $tmpstr = $tpl -> mergeTemplate();
      $tmpstr = str_replace('{$-pagi-rscount}', $pagi -> rscount, $tmpstr);
      $tmpstr = str_replace('{$-pagi-pagenum}', $pagi -> pagenum, $tmpstr);
      $tmpstr = str_replace('{$-pagi-pagetotal}', $pagi -> pagetotal, $tmpstr);
      $batchList = '';
      if ($account -> checkPopedom(self::getPara('genre'), 'delete')) $batchList .= ',delete';
      $tmpstr = str_replace('{$-batch-list}', $batchList, $tmpstr);
      $tmpstr = str_replace('{$-batch-show}', empty($batchList) ? 0 : 1, $tmpstr);
      $tmpstr = tpl::parse($tmpstr);
      $tmpstr = $account -> replaceAccountTag($tmpstr);
    }
    $tmpstr = self::formatResult($status, $tmpstr);
    return $tmpstr;
  }

  public static function moduleActionAdd()
  {
    $status = 0;
    $message = '';
    $para = '';
    $account = self::account();
    if (!($account -> checkPopedom(self::getPara('genre'), 'add')))
    {
      $message = tpl::take('::console.text-tips-error-403', 'lng');
    }
    else
    {
      $db = self::db();
      if (!is_null($db))
      {
        $upResult = upload::up2self(@$_FILES['file']);
        $upResultArray = json_decode($upResult, 1);
        if (is_array($upResultArray))
        {
          $status = $upResultArray['status'];
          $message = $upResultArray['message'];
          $para = $upResultArray['para'];
          if ($status == 1)
          {
            $paraArray = json_decode($para, 1);
            if (is_array($paraArray))
            {
              $table = tpl::take('config.db_table', 'cfg');
              $prefix = tpl::take('config.db_prefix', 'cfg');
              $preset = array();
              $preset[$prefix . 'topic'] = $paraArray['filename'];
              $preset[$prefix . 'filepath'] = $paraArray['filepath'];
              $preset[$prefix . 'fileurl'] = $paraArray['fileurl'];
              $preset[$prefix . 'filetype'] = $paraArray['filetype'];
              $preset[$prefix . 'filesize'] = $paraArray['filesize'];
              $preset[$prefix . 'filegroup'] = base::getFileGroup($paraArray['filetype']);
              $preset[$prefix . 'lang'] = $account -> getLang();
              $preset[$prefix . 'time'] = base::getDateTime();
              $sqlstr = smart::getAutoInsertSQLByVars($table, $preset);
              $re = $db -> exec($sqlstr);
              if (is_numeric($re))
              {
                $logString = tpl::take('manage.log-add-1', 'lng');
                $logString = str_replace('{$id}', $db -> lastInsertId, $logString);
                $logString = str_replace('{$filepath}', $paraArray['filepath'], $logString);
                $account -> creatLog(self::getPara('genre'), $logString, request::getRemortIP());
              }
            }
          }
        }
      }
    }
    $tmpstr = self::formatMsgResult($status, $message, $para);
    return $tmpstr;
  }

  public static function moduleActionReplace()
  {
    $status = 0;
    $message = '';
    $account = self::account();
    $id = base::getNum(request::getHTTPPara('id', 'get'), 0);
    if (!($account -> checkPopedom(self::getPara('genre'), 'edit')))
    {
      $message = tpl::take('::console.text-tips-error-403', 'lng');
    }
    else
    {
      $db = self::db();
      if (!is_null($db))
      {
        $table = tpl::take('config.db_table', 'cfg');
        $prefix = tpl::take('config.db_prefix', 'cfg');
        $sqlstr = "select * from " . $table . " where " . $prefix . "delete=0 and " . $prefix . "id=" . $id;
        $rq = $db -> query($sqlstr);
        $rs = $rq -> fetch();
        if (is_array($rs))
        {
          $rsFilePath = base::getString($rs[$prefix . 'filepath']);
          $upResult = upload::up2self(@$_FILES['file'], '', $rsFilePath);
          $upResultArray = json_decode($upResult, 1);
          if (is_array($upResultArray))
          {
            $status = $upResultArray['status'];
            $message = $upResultArray['message'];
            $para = $upResultArray['para'];
            if ($status == 1)
            {
              $paraArray = json_decode($para, 1);
              if (is_array($paraArray))
              {
                $preset = array();
                $preset[$prefix . 'topic'] = $paraArray['filename'];
                $preset[$prefix . 'filesize'] = $paraArray['filesize'];
                $sqlstr = smart::getAutoUpdateSQLByVars($table, $prefix . 'id', $id, $preset);
                $re = $db -> exec($sqlstr);
                if (is_numeric($re))
                {
                  $logString = tpl::take('manage.log-replace-1', 'lng');
                  $logString = str_replace('{$id}', $id, $logString);
                  $account -> creatLog(self::getPara('genre'), $logString, request::getRemortIP());
                }
              }
            }
          }
        }
      }
    }
    $tmpstr = self::formatMsgResult($status, $message);
    return $tmpstr;
  }

  public static function moduleActionEdit()
  {
    $tmpstr = '';
    $status = 0;
    $message = '';
    $error = array();
    $account = self::account();
    $id = base::getNum(request::getHTTPPara('id', 'get'), 0);
    $topic = request::getHTTPPara('topic', 'post');
    if (!$account -> checkPopedom(self::getPara('genre'), 'edit'))
    {
      array_push($error, tpl::take('::console.text-tips-error-403', 'lng'));
    }
    else
    {
      if (base::isEmpty($topic)) array_push($error, tpl::take('manage.text-tips-edit-error-1', 'lng'));
      if (count($error) == 0)
      {
        $db = self::db();
        if (!is_null($db))
        {
          $table = tpl::take('config.db_table', 'cfg');
          $prefix = tpl::take('config.db_prefix', 'cfg');
          $specialFiled = $prefix . 'id,' . $prefix . 'filepath,' . $prefix . 'fileurl,' . $prefix . 'filetype,' . $prefix . 'filesize,' . $prefix . 'filegroup,' . $prefix . 'hot,' . $prefix . 'delete';
          $preset = array();
          $preset[$prefix . 'lang'] = $account -> getLang();
          $sqlstr = smart::getAutoRequestUpdateSQL($table, $specialFiled, $prefix . 'id', $id, $preset);
          $re = $db -> exec($sqlstr);
          if (is_numeric($re))
          {
            $status = 1;
            $message = tpl::take('manage.text-tips-edit-done', 'lng');
            $logString = tpl::take('manage.log-edit-1', 'lng');
            $logString = str_replace('{$id}', $id, $logString);
            $account -> creatLog(self::getPara('genre'), $logString, request::getRemortIP());
          }
        }
      }
    }
    if (count($error) != 0) $message = implode('|', $error);
    $tmpstr = self::formatMsgResult($status, $message);
    return $tmpstr;
  }

  public static function moduleActionBatch()
  {
    $tmpstr = '';
    $status = 0;
    $message = '';
    $account = self::account();
    $ids = base::getString(request::getHTTPPara('ids', 'get'));
    $batch = base::getString(request::getHTTPPara('batch', 'get'));
    if (base::cIdAry($ids))
    {
      $table = tpl::take('config.db_table', 'cfg');
      $prefix = tpl::take('config.db_prefix', 'cfg');
      if ($batch == 'delete' && $account -> checkPopedom(self::getPara('genre'), 'delete'))
      {
        if (smart::dbFieldSwitch($table, $prefix, 'delete', $ids)) $status = 1;
      }
      if ($status == 1)
      {
        $logString = tpl::take('manage.log-batch-1', 'lng');
        $logString = str_replace('{$id}', $ids, $logString);
        $logString = str_replace('{$batch}', $batch, $logString);
        $account -> creatLog(self::getPara('genre'), $logString, request::getRemortIP());
      }
    }
    $tmpstr = self::formatMsgResult($status, $message);
    return $tmpstr;
  }

  public static function moduleActionDelete()
  {
    $tmpstr = '';
    $status = 0;
    $message = '';
    $id = base::getNum(request::getHTTPPara('id', 'get'), 0);
    $account = self::account();
    if (!$account -> checkPopedom(self::getPara('genre'), 'delete'))
    {
      $message = tpl::take('::console.text-tips-error-403', 'lng');
    }
    else
    {
      $table = tpl::take('config.db_table', 'cfg');
      $prefix = tpl::take('config.db_prefix', 'cfg');
      if (smart::dbFieldSwitch($table, $prefix, 'delete', $id, 1))
      {
        $status = 1;
        $logString = tpl::take('manage.log-delete-1', 'lng');
        $logString = str_replace('{$id}', $id, $logString);
        $account -> creatLog(self::getPara('genre'), $logString, request::getRemortIP());
      }
    }
    $tmpstr = self::formatMsgResult($status, $message);
    return $tmpstr;
  }

  public static function getResult()
  {
    $tmpstr = '';
    $account = self::account();
    if ($account -> checkLogin())
    {
      if ($account -> checkPopedom(self::getPara('genre')))
      {
        $tmpstr = parent::getResult();
      }
    }
    return $tmpstr;
  }
}
?>
