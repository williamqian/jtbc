<?php
namespace jtbc;
class ui extends page {
  public static function start()
  {
    self::setPageTitle(tpl::take('index.title', 'lng'));
  }

  public static function moduleDetail()
  {
    $tmpstr = '';
    $id = base::getNum(request::getHTTPPara('id', 'get'), 0);
    $db = self::db();
    if (!is_null($db))
    {
      $table = tpl::take('config.db_table', 'cfg');
      $prefix = tpl::take('config.db_prefix', 'cfg');
      $sqlstr = "select * from " . $table . " where " . $prefix . "delete=0 and " . $prefix . "publish=1 and " . $prefix . "id=" . $id;
      $rq = $db -> query($sqlstr);
      $rs = $rq -> fetch();
      if (is_array($rs))
      {
        $tmpstr = tpl::take('index.detail', 'tpl');
        $rsTopic = base::getString($rs[$prefix . 'topic']);
        self::setPageTitle(base::htmlEncode($rsTopic));
        foreach ($rs as $key => $val)
        {
          $key = base::getLRStr($key, '_', 'rightr');
          $GLOBALS['RS_' . $key] = $val;
          $tmpstr = str_replace('{$' . $key . '}', base::htmlEncode($val), $tmpstr);
        }
        $tmpstr = tpl::parse($tmpstr);
      }
    }
    return $tmpstr;
  }

  public static function moduleList()
  {
    $status = 1;
    $tmpstr = '';
    $page = base::getNum(request::getHTTPPara('page', 'get'), 0);
    $category = base::getNum(request::getHTTPPara('category', 'get'), 0);
    $pagesize = base::getNum(tpl::take('config.pagesize', 'cfg'), 0);
    $db = self::db();
    if (!is_null($db))
    {
      $tmpstr = tpl::take('index.list', 'tpl');
      $tpl = new tpl();
      $tpl -> tplString = $tmpstr;
      $loopString = $tpl -> getLoopString('{@}');
      $table = tpl::take('config.db_table', 'cfg');
      $prefix = tpl::take('config.db_prefix', 'cfg');
      $sqlstr = "select * from " . $table . " where " . $prefix . "delete=0 and " . $prefix . "publish=1 and " . $prefix . "lang=" . smart::getForeLang();
      if ($category != 0)
      {
        self::setPageTitle(base::htmlEncode(universal\category::getCategoryTopicByID(self::getPara('genre'), smart::getForeLang(), $category)));
        $sqlstr .= " and " . $prefix . "category in (" . base::mergeIdAry($category, universal\category::getCategoryChildID(self::getPara('genre'), smart::getForeLang(), $category)) . ")";
      }
      $sqlstr .=" order by " . $prefix . "time desc";
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
          $loopLineString = tpl::parse($loopLineString);
          $tpl -> insertLoopLine($loopLineString);
        }
      }
      $tmpstr = $tpl -> mergeTemplate();
      $tmpstr = str_replace('{$-category}', base::htmlEncode($category), $tmpstr);
      $tmpstr = str_replace('{$-pagi-rscount}', $pagi -> rscount, $tmpstr);
      $tmpstr = str_replace('{$-pagi-pagenum}', $pagi -> pagenum, $tmpstr);
      $tmpstr = str_replace('{$-pagi-pagetotal}', $pagi -> pagetotal, $tmpstr);
      $tmpstr = tpl::parse($tmpstr);
    }
    return $tmpstr;
  }

  public static function moduleDefault()
  {
    $tmpstr = tpl::take('index.default', 'tpl');
    $tmpstr = tpl::parse($tmpstr);
    if (base::isEmpty($tmpstr)) $tmpstr = self::moduleList();
    return $tmpstr;
  }
}
?>
