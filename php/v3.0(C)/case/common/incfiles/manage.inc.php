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

  public static function moduleAdd()
  {
    $status = 1;
    $tmpstr = '';
    $category = base::getNum(self::getHTTPPara('category', 'get'), 0);
    $account = self::account();
    if ($account -> checkPopedom(self::getPara('genre'), 'add'))
    {
      $tmpstr = tpl::take('manage.add', 'tpl');
      $tmpstr = str_replace('{$-category-nav}', universal\category::getCategoryNavByID(self::getPara('genre'), $account -> getLang(), $category), $tmpstr);
      $tmpstr = str_replace('{$-category-select}', universal\category::getCategorySelectByGenre(self::getPara('genre'), $account -> getLang(), $account -> getGenrePopedom(self::getPara('genre'), 'category'), 'id=' . $category), $tmpstr);
      $tmpstr = tpl::parse($tmpstr);
      $tmpstr = $account -> replaceAccountTag($tmpstr);
    }
    $tmpstr = self::formatResult($status, $tmpstr);
    return $tmpstr;
  }

  public static function moduleEdit()
  {
    $status = 1;
    $tmpstr = '';
    $id = base::getNum(self::getHTTPPara('id', 'get'), 0);
    $category = base::getNum(self::getHTTPPara('category', 'get'), 0);
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
          $rscategory = base::getNum($rs[$prefix . 'category'], 0);
          foreach ($rs as $key => $val)
          {
            $key = base::getLRStr($key, '_', 'rightr');
            $GLOBALS['RS_' . $key] = $val;
            $tmpstr = str_replace('{$' . $key . '}', base::htmlEncode($val), $tmpstr);
          }
          $tmpstr = str_replace('{$-category-nav}', universal\category::getCategoryNavByID(self::getPara('genre'), $account -> getLang(), $category), $tmpstr);
          $tmpstr = str_replace('{$-category-select}', universal\category::getCategorySelectByGenre(self::getPara('genre'), $account -> getLang(), $account -> getGenrePopedom(self::getPara('genre'), 'category'), 'id=' . $rscategory), $tmpstr);
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
    $page = base::getNum(self::getHTTPPara('page', 'get'), 0);
    $publish = base::getNum(self::getHTTPPara('publish', 'get'), -1);
    $category = base::getNum(self::getHTTPPara('category', 'get'), 0);
    $keyword = base::getString(self::getHTTPPara('keyword', 'get'));
    $pagesize = base::getNum(tpl::take('config.pagesize', 'cfg'), 0);
    $db = self::db();
    if (!is_null($db))
    {
      $account = self::account();
      $myCategory = $account -> getGenrePopedom(self::getPara('genre'), 'category');
      $tmpstr = tpl::take('manage.list', 'tpl');
      $tpl = new tpl();
      $tpl -> tplString = $tmpstr;
      $loopString = $tpl -> getLoopString('{@}');
      $table = tpl::take('config.db_table', 'cfg');
      $prefix = tpl::take('config.db_prefix', 'cfg');
      $sqlstr = "select * from " . $table . " where " . $prefix . "delete=0 and " . $prefix . "lang=" . $account -> getLang();
      if ($publish != -1) $sqlstr .= " and " . $prefix . "publish=" . $publish;
      if (!base::isEmpty($myCategory) && base::cIdAry($myCategory)) $sqlstr .= " and " . $prefix . "category in (" . $myCategory . ")";
      if ($category != 0) $sqlstr .= " and " . $prefix . "category in (" . base::mergeIdAry($category, universal\category::getCategoryChildID(self::getPara('genre'), $account -> getLang(), $category)) . ")";
      if (!base::isEmpty($keyword)) $sqlstr .= smart::getCutKeywordSQL($prefix . 'topic', $keyword);
      $sqlstr .=" order by " . $prefix . "time desc";
      $pagi = new pagi($db);
      $rsAry = $pagi -> getDataAry($sqlstr, $page, $pagesize);
      if (is_array($rsAry))
      {
        foreach($rsAry as $rs)
        {
          $loopLineString = $loopString;
          $rscategory = base::getNum($rs[$prefix . 'category'], 0);
          foreach ($rs as $key => $val)
          {
            $key = base::getLRStr($key, '_', 'rightr');
            $GLOBALS['RS_' . $key] = $val;
            $loopLineString = str_replace('{$' . $key . '}', base::htmlEncode($val), $loopLineString);
            if (!is_numeric($key) && $key == 'topic') $loopLineString = str_replace('{$-topic-keyword-highlight}', smart::replaceKeyWordHighlight(base::htmlEncode(smart::replaceKeyWordHighlight($val, $keyword))), $loopLineString);
          }
          $loopLineString = str_replace('{$-category-topic}', base::htmlEncode(universal\category::getCategoryTopicByID(self::getPara('genre'), $account -> getLang(), $rscategory)), $loopLineString);
          $tpl -> insertLoopLine($loopLineString);
        }
      }
      $tmpstr = $tpl -> mergeTemplate();
      $tmpstr = str_replace('{$-keyword}', base::htmlEncode($keyword), $tmpstr);
      $tmpstr = str_replace('{$-category}', base::htmlEncode($category), $tmpstr);
      $tmpstr = str_replace('{$-category-nav}', universal\category::getCategoryNavByID(self::getPara('genre'), $account -> getLang(), $category), $tmpstr);
      $tmpstr = str_replace('{$-pagi-rscount}', $pagi -> rscount, $tmpstr);
      $tmpstr = str_replace('{$-pagi-pagenum}', $pagi -> pagenum, $tmpstr);
      $tmpstr = str_replace('{$-pagi-pagetotal}', $pagi -> pagetotal, $tmpstr);
      $batchList = '';
      if ($account -> checkPopedom(self::getPara('genre'), 'publish')) $batchList .= ',publish';
      if ($account -> checkPopedom(self::getPara('genre'), 'delete')) $batchList .= ',delete';
      $tmpstr = str_replace('{$-batch-list}', $batchList, $tmpstr);
      $tmpstr = str_replace('{$-batch-show}', empty($batchList) ? 0 : 1, $tmpstr);
      $tmpstr = tpl::parse($tmpstr);
      $tmpstr = $account -> replaceAccountTag($tmpstr);
    }
    $tmpstr = self::formatResult($status, $tmpstr);
    return $tmpstr;
  }

  public static function moduleCategory()
  {
    $status = 1;
    $tmpstr = '';
    $fid = base::getNum(self::getHTTPPara('fid', 'get'), 0);
    $account = self::account();
    if ($account -> checkPopedom(self::getPara('genre')))
    {
      $prefix = universal\category::getPrefix();
      $myCategory = $account -> getGenrePopedom(self::getPara('genre'), 'category');
      $categoryAry = universal\category::getCategoryAryByGenre(self::getPara('genre'), $account -> getLang());
      $tmpstr = tpl::take('manage.category', 'tpl');
      $tpl = new tpl();
      $tpl -> tplString = $tmpstr;
      $loopString = $tpl -> getLoopString('{@}');
      foreach ($categoryAry as $myKey => $myVal)
      {
        if (is_array($myVal))
        {
          $rsid = base::getNum($myVal[$prefix . 'id'], -1);
          $rsfid = base::getNum($myVal[$prefix . 'fid'], -1);
          if ($rsfid == $fid && (base::isEmpty($myCategory) || base::cInstr($myCategory, $rsid)))
          {
            $loopLineString = $loopString;
            foreach ($myVal as $key => $val)
            {
              $key = base::getLRStr($key, '_', 'rightr');
              $loopLineString = str_replace('{$' . $key . '}', base::htmlEncode($val), $loopLineString);
            }
            $tpl -> insertLoopLine($loopLineString);
          }
        }
      }
      $tmpstr = $tpl -> mergeTemplate();
      $tmpstr = tpl::parse($tmpstr);
    }
    $tmpstr = self::formatResult($status, $tmpstr);
    return $tmpstr;
  }

  public static function moduleActionAdd()
  {
    $tmpstr = '';
    $status = 0;
    $message = '';
    $error = array();
    $account = self::account();
    $topic = self::getHTTPPara('topic', 'post');
    $image = self::getHTTPPara('image', 'post');
    $category = base::getNum(self::getHTTPPara('category', 'post'), 0);
    if (!$account -> checkPopedom(self::getPara('genre'), 'add') || !$account -> checkPopedomByCategory(self::getPara('genre'), $category))
    {
      array_push($error, tpl::take('::console.text-tips-error-403', 'lng'));
    }
    else
    {
      if (base::isEmpty($topic)) array_push($error, tpl::take('manage.text-tips-add-error-1', 'lng'));
      if (base::isEmpty($image)) array_push($error, tpl::take('manage.text-tips-add-error-2', 'lng'));
      if (count($error) == 0)
      {
        $db = self::db();
        if (!is_null($db))
        {
          $table = tpl::take('config.db_table', 'cfg');
          $prefix = tpl::take('config.db_prefix', 'cfg');
          $specialFiled = $prefix . 'id,' . $prefix . 'delete';
          $preset = array();
          $preset[$prefix . 'publish'] = 0;
          $preset[$prefix . 'lang'] = $account -> getLang();
          $preset[$prefix . 'time'] = base::getDateTime();
          if ($account -> checkPopedom(self::getPara('genre'), 'publish')) $preset[$prefix . 'publish'] = base::getNum(self::getHTTPPara('publish', 'post'), 0);
          $sqlstr = smart::getAutoRequestInsertSQL($table, $specialFiled, $preset);
          $re = $db -> exec($sqlstr);
          if (is_numeric($re))
          {
            $status = 1;
            $logString = tpl::take('manage.log-add-1', 'lng');
            $logString = str_replace('{$id}', $db -> lastInsertId, $logString);
            $account -> creatLog(self::getPara('genre'), $logString, self::getRemortIP());
          }
        }
      }
    }
    if (count($error) != 0) $message = implode('|', $error);
    $tmpstr = self::formatXMLResult($status, $message);
    return $tmpstr;
  }

  public static function moduleActionEdit()
  {
    $tmpstr = '';
    $status = 0;
    $message = '';
    $error = array();
    $account = self::account();
    $id = base::getNum(self::getHTTPPara('id', 'get'), 0);
    $topic = self::getHTTPPara('topic', 'post');
    $image = self::getHTTPPara('image', 'post');
    $category = base::getNum(self::getHTTPPara('category', 'post'), 0);
    if (!$account -> checkPopedom(self::getPara('genre'), 'edit') || !$account -> checkPopedomByCategory(self::getPara('genre'), $category))
    {
      array_push($error, tpl::take('::console.text-tips-error-403', 'lng'));
    }
    else
    {
      if (base::isEmpty($topic)) array_push($error, tpl::take('manage.text-tips-edit-error-1', 'lng'));
      if (base::isEmpty($image)) array_push($error, tpl::take('manage.text-tips-edit-error-2', 'lng'));
      if (count($error) == 0)
      {
        $db = self::db();
        if (!is_null($db))
        {
          $table = tpl::take('config.db_table', 'cfg');
          $prefix = tpl::take('config.db_prefix', 'cfg');
          $specialFiled = $prefix . 'id,' . $prefix . 'delete';
          $preset = array();
          $preset[$prefix . 'publish'] = 0;
          $preset[$prefix . 'lang'] = $account -> getLang();
          if ($account -> checkPopedom(self::getPara('genre'), 'publish')) $preset[$prefix . 'publish'] = base::getNum(self::getHTTPPara('publish', 'post'), 0);
          $sqlstr = smart::getAutoRequestUpdateSQL($table, $specialFiled, $prefix . 'id', $id, $preset);
          $re = $db -> exec($sqlstr);
          if (is_numeric($re))
          {
            $status = 1;
            $message = tpl::take('manage.text-tips-edit-done', 'lng');
            $logString = tpl::take('manage.log-edit-1', 'lng');
            $logString = str_replace('{$id}', $id, $logString);
            $account -> creatLog(self::getPara('genre'), $logString, self::getRemortIP());
          }
        }
      }
    }
    if (count($error) != 0) $message = implode('|', $error);
    $tmpstr = self::formatXMLResult($status, $message);
    return $tmpstr;
  }

  public static function moduleActionBatch()
  {
    $tmpstr = '';
    $status = 0;
    $message = '';
    $account = self::account();
    $ids = base::getString(self::getHTTPPara('ids', 'get'));
    $batch = base::getString(self::getHTTPPara('batch', 'get'));
    if (base::cIdAry($ids))
    {
      $table = tpl::take('config.db_table', 'cfg');
      $prefix = tpl::take('config.db_prefix', 'cfg');
      if ($batch == 'delete' && $account -> checkPopedom(self::getPara('genre'), 'delete'))
      {
        if (smart::dbFieldSwitch($table, $prefix, 'delete', $ids)) $status = 1;
      }
      else if ($batch == 'publish' && $account -> checkPopedom(self::getPara('genre'), 'publish'))
      {
        if (smart::dbFieldSwitch($table, $prefix, 'publish', $ids)) $status = 1;
      }
      if ($status == 1)
      {
        $logString = tpl::take('manage.log-batch-1', 'lng');
        $logString = str_replace('{$id}', $ids, $logString);
        $logString = str_replace('{$batch}', $batch, $logString);
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
    $id = base::getNum(self::getHTTPPara('id', 'get'), 0);
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
        $account -> creatLog(self::getPara('genre'), $logString, self::getRemortIP());
      }
    }
    $tmpstr = self::formatXMLResult($status, $message);
    return $tmpstr;
  }

  public static function moduleActionUpload()
  {
    $status = 0;
    $message = '';
    $para = '';
    $limit = base::getString(self::getHTTPPara('limit', 'get'));
    $account = self::account();
    if (!($account -> checkPopedom(self::getPara('genre'), 'add') || $account -> checkPopedom(self::getPara('genre'), 'edit')))
    {
      $message = tpl::take('::console.text-tips-error-403', 'lng');
    }
    else
    {
      $upResult = upload::up2self(@$_FILES['file'], $limit);
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
            $logString = tpl::take('manage.log-upload-1', 'lng');
            $logString = str_replace('{$filepath}', $paraArray['filepath'], $logString);
            $account -> creatLog(self::getPara('genre'), $logString, self::getRemortIP());
          }
        }
      }
    }
    $tmpstr = self::formatXMLResult($status, $message, $para);
    return $tmpstr;
  }

  public static function moduleAction()
  {
    $tmpstr = '';
    $action = self::getHTTPPara('action', 'get');
    switch($action)
    {
      case 'add':
        $tmpstr = self::moduleActionAdd();
        break;
      case 'edit':
        $tmpstr = self::moduleActionEdit();
        break;
      case 'batch':
        $tmpstr = self::moduleActionBatch();
        break;
      case 'delete':
        $tmpstr = self::moduleActionDelete();
        break;
      case 'upload':
        $tmpstr = self::moduleActionUpload();
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
          case 'add':
            $tmpstr = self::moduleAdd();
            break;
          case 'edit':
            $tmpstr = self::moduleEdit();
            break;
          case 'list':
            $tmpstr = self::moduleList();
            break;
          case 'category':
            $tmpstr = self::moduleCategory();
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
