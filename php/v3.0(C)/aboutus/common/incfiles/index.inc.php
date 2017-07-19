<?php
namespace jtbc;
class ui extends page {
  public static function moduleDetail()
  {
    $tmpstr = '';
    $id = base::getNum(self::getHTTPPara('id', 'get'), 0);
    $db = self::db();
    if (!is_null($db))
    {
      $table = tpl::take('config.db_table', 'cfg');
      $prefix = tpl::take('config.db_prefix', 'cfg');
      $sqlstr = "select * from " . $table . " where " . $prefix . "delete=0 and " . $prefix . "publish=1";
      if ($id != 0) $sqlstr .= " and " . $prefix . "id=" . $id;
      $sqlstr .= " order by " . $prefix . "time desc limit 0,1";
      $rq = $db -> query($sqlstr);
      $rs = $rq -> fetch();
      if (is_array($rs))
      {
        $tmpstr = tpl::take('index.detail', 'tpl');
        $rsTopic = base::getString($rs[$prefix . 'topic']);
        self::setTitle(base::htmlEncode($rsTopic));
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

  public static function getResult()
  {
    $tmpstr = '';
    $type = self::getHTTPPara('type', 'get');
    self::setTitle(tpl::take('index.title', 'lng'));
    switch($type)
    {
      case 'detail':
        $tmpstr = self::moduleDetail();
        break;
      default:
        $tmpstr = self::moduleDetail();
        break;
    }
    return $tmpstr;
  }
}
?>
