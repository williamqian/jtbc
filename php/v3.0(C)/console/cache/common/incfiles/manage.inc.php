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
    $path = smart::getActualRoute('cache/', 1);
    $db = self::db();
    if (!is_null($db))
    {
      $account = self::account();
      $tmpstr = tpl::take('manage.list', 'tpl');
      $tpl = new tpl();
      $tpl -> tplString = $tmpstr;
      $loopString = $tpl -> getLoopString('{@}');
      if (is_dir($path))
      {
        $dir = @dir($path);
        while($entry = $dir -> read())
        {
          if (is_file($path . $entry) && is_numeric(strpos($entry, '.inc.php')))
          {
            $loopLineString = $loopString;
            $loopLineString = str_replace('{$topic}', base::htmlEncode(base::getLRStr($entry, '.inc.php', 'leftr')), $loopLineString);
            $loopLineString = str_replace('{$lasttime}', base::htmlEncode(date('Y-m-d H:i:s', filemtime($path . $entry))), $loopLineString);
            $loopLineString = str_replace('{$size}', base::htmlEncode(base::formatFileSize(filesize($path . $entry))), $loopLineString);
            $tpl -> insertLoopLine($loopLineString);
          }
        }
      }
      $tmpstr = $tpl -> mergeTemplate();
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
    $ids = base::getString(self::getHTTPPara('ids', 'post'));
    $batch = base::getString(self::getHTTPPara('batch', 'get'));
    if ($batch == 'delete' && $account -> checkPopedom(self::getPara('genre'), 'delete'))
    {
      $idAry = explode(',', $ids);
      foreach ($idAry as $key => $val)
      {
        if (!base::isEmpty($val))
        {
          if (cache::remove($val)) $status = 1;
        }
      }
    }
    if ($status == 1)
    {
      $logString = tpl::take('manage.log-batch-1', 'lng');
      $logString = str_replace('{$batch}', $batch, $logString);
      $account -> creatLog(self::getPara('genre'), $logString, self::getRemortIP());
    }
    $tmpstr = self::formatXMLResult($status, $message);
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
      if (cache::remove())
      {
        $status = 1;
        $logString = tpl::take('manage.log-empty-1', 'lng');
        $account -> creatLog(self::getPara('genre'), $logString, self::getRemortIP());
      }
    }
    $tmpstr = self::formatXMLResult($status, $message);
    return $tmpstr;
  }

  public static function moduleActionDelete()
  {
    $tmpstr = '';
    $status = 0;
    $message = '';
    $id = base::getString(self::getHTTPPara('id', 'get'));
    $account = self::account();
    if (!$account -> checkPopedom(self::getPara('genre'), 'delete'))
    {
      $message = tpl::take('::console.text-tips-error-403', 'lng');
    }
    else
    {
      if (cache::remove($id))
      {
        $status = 1;
        $logString = tpl::take('manage.log-delete-1', 'lng');
        $logString = str_replace('{$id}', $id, $logString);
        $account -> creatLog(self::getPara('genre'), $logString, self::getRemortIP());
      }
    }
    $tmpstr = self::formatXMLResult($status, $message);
    return $tmpstr;
  }

  public static function moduleAction()
  {
    $tmpstr = '';
    $action = self::getHTTPPara('action', 'get');
    switch($action)
    {
      case 'batch':
        $tmpstr = self::moduleActionBatch();
        break;
      case 'empty':
        $tmpstr = self::moduleActionEmpty();
        break;
      case 'delete':
        $tmpstr = self::moduleActionDelete();
        break;
    }
    return $tmpstr;
  }

  public static function getResult()
  {
    $tmpstr = '';
    $account = self::account();
    $type = self::getHTTPPara('type', 'get');
    if ($account -> checkLogin())
    {
      if ($account -> checkPopedom(self::getPara('genre')))
      {
        switch($type)
        {
          case 'list':
            $tmpstr = self::moduleList();
            break;
          case 'action':
            $tmpstr = self::moduleAction();
            break;
          default:
            $tmpstr = self::moduleList();
            break;
        }
      }
    }
    return $tmpstr;
  }
}
?>
