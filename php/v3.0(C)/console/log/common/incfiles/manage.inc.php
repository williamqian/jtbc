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

  public static function moduleList()
  {
    $status = 1;
    $tmpstr = '';
    $page = base::getNum(request::getHTTPPara('page', 'get'), 0);
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
      $sqlstr = "select * from " . $table . " where " . $prefix . "delete=0 order by " . $prefix . "id desc";
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

  public static function moduleActionEmpty()
  {
    $tmpstr = '';
    $status = 0;
    $message = '';
    $account = self::account();
    if (!$account -> checkPopedom(self::getPara('genre'), 'empty'))
    {
      $message = tpl::take('::console.text-tips-error-403', 'lng');
    }
    else
    {
      $table = tpl::take('config.db_table', 'cfg');
      $db = self::db();
      if (!is_null($db))
      {
        $re = $db -> exec('truncate table ' . $table);
        if (is_numeric($re))
        {
          $status = 1;
          $logString = tpl::take('manage.log-empty-1', 'lng');
          $account -> creatLog(self::getPara('genre'), $logString, request::getRemortIP());
        }
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
