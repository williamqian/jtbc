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
      $sql = new sql($db, $table, $prefix, 'time');
      $sql -> publish = 1;
      if ($id != 0) $sql -> id = $id;
      $sqlstr = $sql -> sql . " limit 0,1";
      $rq = $db -> query($sqlstr);
      $rs = $rq -> fetch();
      if (is_array($rs))
      {
        $rsTopic = base::getString($rs[$prefix . 'topic']);
        self::setPageTitle(base::htmlEncode($rsTopic));
        $tmpstr = tpl::take('index.detail', 'tpl');
        $tmpstr = tpl::replaceTagByAry($tmpstr, $rs, 10);
        $tmpstr = tpl::parse($tmpstr);
      }
    }
    return $tmpstr;
  }

  public static function moduleDefault()
  {
    $tmpstr = tpl::take('index.default', 'tpl');
    $tmpstr = tpl::parse($tmpstr);
    if (base::isEmpty($tmpstr)) $tmpstr = self::moduleDetail();
    return $tmpstr;
  }
}
?>
