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

  protected static function ppGetSelectRoleHTML($argRole = -1)
  {
    $tmpstr = '';
    $role = base::getNum($argRole, -1);
    $db = self::db();
    if (!is_null($db))
    {
      $optionUnselected = tpl::take('global.config.xmlselect_unselect', 'tpl');
      $optionselected = tpl::take('global.config.xmlselect_select', 'tpl');
      if ($role == -1) $tmpstr .= $optionselected;
      else $tmpstr .= $optionUnselected;
      $tmpstr = str_replace('{$explain}', tpl::take(':/role:manage.text-super', 'lng'), $tmpstr);
      $tmpstr = str_replace('{$value}', '-1', $tmpstr);
      $table = tpl::take(':/role:config.db_table', 'cfg');
      $prefix = tpl::take(':/role:config.db_prefix', 'cfg');
      $sqlstr = "select * from " . $table . " where " . $prefix . "delete=0 order by " . $prefix . "time desc";
      $rq = $db -> query($sqlstr);
      while($rs = $rq -> fetch())
      {
        $rsID = base::getNum($rs[$prefix . 'id'], 0);
        $rsTopic = base::getString($rs[$prefix . 'topic']);
        if ($role == $rsID) $tmpstr .= $optionselected;
        else $tmpstr .= $optionUnselected;
        $tmpstr = str_replace('{$explain}', base::htmlEncode($rsTopic), $tmpstr);
        $tmpstr = str_replace('{$value}', $rsID, $tmpstr);
      }
    }
    return $tmpstr;
  }

  public static function moduleAdd()
  {
    $status = 1;
    $tmpstr = '';
    $account = self::account();
    if ($account -> checkPopedom(self::getPara('genre'), 'add'))
    {
      $tmpstr = tpl::take('manage.add', 'tpl');
      $tmpstr = str_replace('{$-select-role-html}', self::ppGetSelectRoleHTML(), $tmpstr);
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
          $rsRole = base::getNum($rs[$prefix . 'role'], 0);
          foreach ($rs as $key => $val)
          {
            $key = base::getLRStr($key, '_', 'rightr');
            $GLOBALS['RS_' . $key] = $val;
            $tmpstr = str_replace('{$' . $key . '}', base::htmlEncode($val), $tmpstr);
          }
          $tmpstr = str_replace('{$-select-role-html}', self::ppGetSelectRoleHTML($rsRole), $tmpstr);
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
    $lock = base::getNum(request::getHTTPPara('lock', 'get'), 0);
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
      $sqlstr = "select * from " . $table . " where " . $prefix . "delete=0";
      if ($lock == 1) $sqlstr .= " and " . $prefix . "lock=1";
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
          $loopLineString = str_replace('{$-role-topic}', base::htmlEncode($account -> getRoleTopicById($rs[$prefix . 'role'])), $loopLineString);
          $tpl -> insertLoopLine($loopLineString);
        }
      }
      $tmpstr = $tpl -> mergeTemplate();
      $tmpstr = str_replace('{$-pagi-rscount}', $pagi -> rscount, $tmpstr);
      $tmpstr = str_replace('{$-pagi-pagenum}', $pagi -> pagenum, $tmpstr);
      $tmpstr = str_replace('{$-pagi-pagetotal}', $pagi -> pagetotal, $tmpstr);
      $batchList = '';
      if ($account -> checkPopedom(self::getPara('genre'), 'lock')) $batchList .= ',lock';
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
    $tmpstr = '';
    $status = 0;
    $message = '';
    $error = array();
    $account = self::account();
    $username = request::getHTTPPara('username', 'post');
    $password = request::getHTTPPara('password', 'post');
    $cpassword = request::getHTTPPara('cpassword', 'post');
    $email = request::getHTTPPara('email', 'post');
    if (!$account -> checkPopedom(self::getPara('genre'), 'add'))
    {
      array_push($error, tpl::take('::console.text-tips-error-403', 'lng'));
    }
    else
    {
      if (base::isEmpty($username)) array_push($error, tpl::take('manage.text-tips-add-error-1', 'lng'));
      if (base::isEmpty($password)) array_push($error, tpl::take('manage.text-tips-add-error-2', 'lng'));
      if ($password != $cpassword) array_push($error, tpl::take('manage.text-tips-add-error-3', 'lng'));
      if (!verify::isEmail($email)) array_push($error, tpl::take('manage.text-tips-add-error-4', 'lng'));
      if (count($error) == 0)
      {
        $db = self::db();
        if (!is_null($db))
        {
          $table = tpl::take('config.db_table', 'cfg');
          $prefix = tpl::take('config.db_prefix', 'cfg');
          $sqlstr = "select * from " . $table . " where " . $prefix . "username='" . addslashes($username) . "' and " . $prefix . "delete=0";
          $rq = $db -> query($sqlstr);
          $rs = $rq -> fetch();
          if (is_array($rs)) array_push($error, tpl::take('manage.text-tips-add-error-101', 'lng'));
          else
          {
            $specialFiled = $prefix . 'id,' . $prefix . 'lock,' . $prefix . 'lastip,' . $prefix . 'lasttime,' . $prefix . 'delete';
            $preset = array();
            $preset[$prefix . 'password'] = md5($password);
            $preset[$prefix . 'time'] = base::getDateTime();
            $sqlstr = smart::getAutoRequestInsertSQL($table, $specialFiled, $preset);
            $re = $db -> exec($sqlstr);
            if (is_numeric($re))
            {
              $status = 1;
              $logString = tpl::take('manage.log-add-1', 'lng');
              $logString = str_replace('{$id}', $db -> lastInsertId, $logString);
              $account -> creatLog(self::getPara('genre'), $logString, request::getRemortIP());
            }
          }
        }
      }
    }
    if (count($error) != 0) $message = implode('|', $error);
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
    $username = request::getHTTPPara('username', 'post');
    $password = request::getHTTPPara('password', 'post');
    $cpassword = request::getHTTPPara('cpassword', 'post');
    $email = request::getHTTPPara('email', 'post');
    if (!$account -> checkPopedom(self::getPara('genre'), 'edit'))
    {
      array_push($error, tpl::take('::console.text-tips-error-403', 'lng'));
    }
    else
    {
      if (base::isEmpty($username)) array_push($error, tpl::take('manage.text-tips-edit-error-1', 'lng'));
      if (!base::isEmpty($password) && $password != $cpassword) array_push($error, tpl::take('manage.text-tips-edit-error-2', 'lng'));
      if (!verify::isEmail($email)) array_push($error, tpl::take('manage.text-tips-edit-error-3', 'lng'));
      if (count($error) == 0)
      {
        $db = self::db();
        if (!is_null($db))
        {
          $table = tpl::take('config.db_table', 'cfg');
          $prefix = tpl::take('config.db_prefix', 'cfg');
          $sqlstr = "select * from " . $table . " where " . $prefix . "username='" . addslashes($username) . "' and " . $prefix . "delete=0 and " . $prefix . "id<>" . $id;
          $rq = $db -> query($sqlstr);
          $rs = $rq -> fetch();
          if (is_array($rs)) array_push($error, tpl::take('manage.text-tips-edit-error-101', 'lng'));
          else
          {
            $specialFiled = $prefix . 'id,' . $prefix . 'password,' . $prefix . 'lock,' . $prefix . 'lastip,' . $prefix . 'lasttime,' . $prefix . 'time,' . $prefix . 'delete';
            $sqlstr = smart::getAutoRequestUpdateSQL($table, $specialFiled, $prefix . 'id', $id);
            $re = $db -> exec($sqlstr);
            if (is_numeric($re))
            {
              $status = 1;
              $message = tpl::take('manage.text-tips-edit-done', 'lng');
              $logString = tpl::take('manage.log-edit-1', 'lng');
              $logString = str_replace('{$id}', $id, $logString);
              $account -> creatLog(self::getPara('genre'), $logString, request::getRemortIP());
              if (!base::isEmpty($password)) $db -> exec("update " . $table . " set " . $prefix . "password='" . md5($password) . "' where " . $prefix . "id=" . $id);
            }
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
      if ($batch == 'lock' && $account -> checkPopedom(self::getPara('genre'), 'lock'))
      {
        if (smart::dbFieldSwitch($table, $prefix, 'lock', $ids)) $status = 1;
      }
      else if ($batch == 'delete' && $account -> checkPopedom(self::getPara('genre'), 'delete'))
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
