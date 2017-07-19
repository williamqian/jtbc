<?php
namespace jtbc;
class ui extends page {
  public static function moduleActionInstall()
  {
    $status = 0;
    $message = '';
    $para = '1';
    $db_host = self::getHTTPPara('db_host', 'post');
    $db_username = self::getHTTPPara('db_username', 'post');
    $db_password = self::getHTTPPara('db_password', 'post');
    $db_database = self::getHTTPPara('db_database', 'post');
    $username = self::getHTTPPara('username', 'post');
    $password = self::getHTTPPara('password', 'post');
    $cpassword = self::getHTTPPara('cpassword', 'post');
    $email = self::getHTTPPara('email', 'post');
    if (SITESTATUS == 0)
    {
      if (base::isEmpty($db_host)) $message = tpl::take('install.text-tips-install-error-1', 'lng');
      else if (base::isEmpty($db_username)) $message = tpl::take('install.text-tips-install-error-2', 'lng');
      else if (base::isEmpty($db_database)) $message = tpl::take('install.text-tips-install-error-4', 'lng');
      else
      {
        $para = '2';
        if (base::isEmpty($username)) $message = tpl::take('install.text-tips-install-error-5', 'lng');
        else if (base::isEmpty($password)) $message = tpl::take('install.text-tips-install-error-6', 'lng');
        else if ($password != $cpassword) $message = tpl::take('install.text-tips-install-error-6c', 'lng');
        else if (!verify::isEmail($email)) $message = tpl::take('install.text-tips-install-error-7', 'lng');
        else
        {
          $db = new db();
          $db -> dbHost = $db_host;
          $db -> dbUsername = $db_username;
          $db -> dbPassword = $db_password;
          $db -> dbDatabase = $db_database;
          $db -> init();
          if ($db -> errStatus != 0)
          {
            $para = '1';
            $message = tpl::take('install.text-tips-install-error-8', 'lng');
          }
          else
          {
            $para = '2';
            $mysql = file_get_contents('mysql.sql');
            $exec = $db -> exec($mysql);
            if (is_numeric($exec))
            {
              self::$db = $db;
              $table = tpl::take(':/account:config.db_table', 'cfg');
              $prefix = tpl::take(':/account:config.db_prefix', 'cfg');
              $preset = array();
              $preset[$prefix . 'username'] = $username;
              $preset[$prefix . 'password'] = md5($password);
              $preset[$prefix . 'email'] = $email;
              $preset[$prefix . 'role'] = -1;
              $preset[$prefix . 'lastip'] = self::getRemortIP();
              $preset[$prefix . 'lasttime'] = base::getDateTime();
              $preset[$prefix . 'time'] = base::getDateTime();
              $sqlstr = smart::getAutoInsertSQLByVars($table, $preset);
              $re = $db -> exec($sqlstr);
              if (is_numeric($re))
              {
                $constPath = smart::getActualRoute('common/incfiles/const.inc.php', 1);
                $constContent = file_get_contents($constPath);
                $constContent = str_replace('{$db_host}', $db_host, $constContent);
                $constContent = str_replace('{$db_username}', $db_username, $constContent);
                $constContent = str_replace('{$db_password}', $db_password, $constContent);
                $constContent = str_replace('{$db_database}', $db_database, $constContent);
                $constContent = str_replace('define(\'SITESTATUS\', 0)', 'define(\'SITESTATUS\', 100)', $constContent);
                $constContentSave = file_put_contents($constPath, $constContent);
                if ($constContentSave)
                {
                  $indexPath = smart::getActualRoute('index.php', 1);
                  $indexContent = file_get_contents($indexPath);
                  $indexContent = str_replace('<?php if (SITESTATUS == 0) header(\'location: _install\');?>', '', $indexContent);
                  $fileBool1 = file_put_contents($indexPath, $indexContent);
                  $completePath = smart::getActualRoute('complete.php', 1);
                  $completeContent = '<?php' . chr(10);
                  $completeContent .= 'require_once(\'common/incfiles/page.inc.php\');' . chr(10);
                  $completeContent .= 'jtbc\\base::removeDir(\'_install\');' . chr(10);
                  $completeContent .= 'unlink(\'complete.php\');' . chr(10);
                  $completeContent .= 'header(\'location: ' . CONSOLEDIR . '\');' . chr(10);
                  $completeContent .= '?>' . chr(10);
                  $fileBool2 = file_put_contents($completePath, $completeContent);
                  if ($fileBool1 && $fileBool2)
                  {
                    $status = 1;
                    $para = smart::getActualRoute('complete.php', 1);
                  }
                  else $message = tpl::take('install.text-tips-install-error-10', 'lng');
                }
                else $message = tpl::take('install.text-tips-install-error-10', 'lng');
              }
              else $message = tpl::take('install.text-tips-install-error-9', 'lng');
            }
            else $message = tpl::take('install.text-tips-install-error-9', 'lng');
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
      case 'install':
        $tmpstr = self::moduleActionInstall();
        break;
    }
    return $tmpstr;
  }

  public static function getResult()
  {
    $tmpstr = '';
    $type = self::getHTTPPara('type', 'get');
    switch($type)
    {
      case 'action':
        $tmpstr = self::moduleAction();
        break;
    }
    return $tmpstr;
  }
}
?>